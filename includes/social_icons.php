<?php

$social_config = [
    'facebook' => '#',
    'twitter' => '#', 
    'tripadvisor' => '#',
    'booking' => '#',
    'instagram' => '#',
    'youtube' => '#',
    'whatsapp' => '#',
    'email' => '#'
];

// Function to render social media icons
function displaySocialIcons($config) {
    $icons_svg = [
        'facebook' => 'fab fa-facebook-f',    
        'twitter' => 'fab fa-twitter',
        'tripadvisor' => '<svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="100" height="100" viewBox="0 0 24 24">
        <path d="M 12 6 C 10.097656 6 8.324219 6.367188 6.8125 7 L 2 7 C 2 7 3.140625 8.394531 3.277344 9.691406 C 2.492188 10.574219 2 11.726563 2 13 C 2 15.761719 4.238281 18 7 18 C 8.378906 18 9.628906 17.441406 10.535156 16.535156 L 12 18 L 13.464844 16.535156 C 14.371094 17.441406 15.621094 18 17 18 C 19.761719 18 22 15.761719 22 13 C 22 11.726563 21.507813 10.574219 20.722656 9.691406 C 20.859375 8.394531 22 7 22 7 L 17.175781 7 C 15.667969 6.367188 13.898438 6 12 6 Z M 12 8 C 13.054688 8 14.078125 8.152344 15.03125 8.40625 C 13.25 9.171875 12 10.9375 12 13 C 12 10.929688 10.742188 9.15625 8.949219 8.394531 C 9.910156 8.140625 10.941406 8 12 8 Z M 7 10 C 8.65625 10 10 11.34375 10 13 C 10 14.65625 8.65625 16 7 16 C 5.34375 16 4 14.65625 4 13 C 4 11.34375 5.34375 10 7 10 Z M 17 10 C 18.65625 10 20 11.34375 20 13 C 20 14.65625 18.65625 16 17 16 C 15.34375 16 14 14.65625 14 13 C 14 11.34375 15.34375 10 17 10 Z M 7 12 C 6.449219 12 6 12.449219 6 13 C 6 13.550781 6.449219 14 7 14 C 7.550781 14 8 13.550781 8 13 C 8 12.449219 7.550781 12 7 12 Z M 17 12 C 16.449219 12 16 12.449219 16 13 C 16 13.550781 16.449219 14 17 14 C 17.550781 14 18 13.550781 18 13 C 18 12.449219 17.550781 12 17 12 Z"></path>
        </svg>', // Assuming this is an SVG file 
        'booking' => '<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 512 512"><rect width="512" height="512" rx="15%" fill="#003580"/><text x="50%" y="56%" font-size="240" fill="#ffffff" font-family="Arial, sans-serif" font-weight="bold" text-anchor="middle" dominant-baseline="middle">B</text></svg>',
        'instagram' => 'fab fa-instagram',    
        'youtube' => 'fab fa-youtube',        
        'whatsapp' => 'fab fa-whatsapp',    
        'email' => 'fas fa-envelope'          
    ];
    
    echo '<div class="social-bookmark-container">';
    
    // Bookmark trigger
    echo '<div class="social-bookmark-tab" onclick="toggleSocialIcons()" title="Social Media">';
    echo '<i class="fas fa-share-alt"></i>';
    echo '</div>';
    
    // Small popup with icons
    echo '<div class="social-popup" id="socialPopup">';
    foreach ($config as $platform => $url) {
        if (!empty($url) && isset($icons_svg[$platform])) {
            $platform_name = ucfirst($platform);
            echo "<a href=\"{$url}\" class=\"social-icon {$platform}\" target=\"_blank\" title=\"{$platform_name}\" rel=\"noopener noreferrer\">";
            // Check if the icon is an SVG or Font Awesome
            if (strpos($icons_svg[$platform], '<svg') !== false) {
                echo $icons_svg[$platform];
            } else {
                echo "<i class=\"{$icons_svg[$platform]}\"></i>";
            }
            echo "</a>";
        }
    }
    echo '</div>';
    
    echo '</div>';
}

// Function to output CSS styles and JavaScript
function getSocialIconsStyles() {
    ?>
    <style>
        .social-bookmark-container {
            position: fixed;
            top: 50%;
            right: 0;
            transform: translateY(-50%);
            z-index: 999999;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        /* Bookmark tab */
        .social-bookmark-tab {
            position: relative;
            width: 45px;
            height: 80px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 8px 0 0 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: -3px 0 15px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
            color: white;
            font-size: 18px;
        }

        .social-bookmark-tab:hover {
            width: 50px;
            background: linear-gradient(135deg, #5a67d8, #6b46c1);
            box-shadow: -5px 0 25px rgba(0, 0, 0, 0.3);
        }

        .social-bookmark-tab:hover i {
            transform: scale(1.1);
        }

        .social-bookmark-tab i {
            transition: transform 0.3s ease;
        }

        /* Small popup */
        .social-popup {
            position: absolute;
            top: 50%;
            right: 50px;
            transform: translateY(-50%);
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            padding: 15px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            box-shadow: 0 10px 30px rgba(255, 255, 255, 0.2);
            opacity: 0;
            visibility: hidden;
            transform: translateY(-50%) translateX(20px) scale(0.9);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .social-popup.active {
            opacity: 1;
            visibility: visible;
            transform: translateY(-50%) translateX(0) scale(1);
        }

        /* Dark mode popup */
        @media (prefers-color-scheme: dark) {
            .social-popup {
                background: rgba(240, 244, 250, 0.95);
                border-color: rgba(177, 203, 240, 0.5);
            }
        }

        /* Social icons in popup */
        .social-icon {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: white;
            font-size: 18px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            position: relative;
            overflow: hidden;
        }

        .social-icon:hover {
            transform: scale(1.1) translateX(-3px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
        }

        .social-icon::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            border-radius: 50%;
            background: linear-gradient(45deg, rgba(255,255,255,0.2), transparent);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .social-icon:hover::before {
            opacity: 1;
        }

        /* Platform colors */
        .facebook { background: linear-gradient(135deg, #3b5998, #2d4373); }
        .twitter { background: linear-gradient(135deg, #1da1f2, #0d8bd9); }
        .instagram { background: linear-gradient(135deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888); }
        .linkedin { background: linear-gradient(135deg, #0077b5, #005885); }
        .youtube { background: linear-gradient(135deg, #ff0000, #cc0000); }
        .whatsapp { background: linear-gradient(135deg, #25d366, #1da851); }
        .email { background: linear-gradient(135deg, #ea4335, #d23321); }
        .tripadvisor { background: linear-gradient(135deg, #34E0A1, #00AA6C); }
        .booking { background: linear-gradient(135deg, #003580, #1a4fa0); }
        /* Responsive */
        @media (max-width: 768px) {
            .social-bookmark-tab {
                width: 40px;
                height: 70px;
                font-size: 16px;
            }
            
            .social-bookmark-tab:hover {
                width: 43px;
            }
            
            .social-popup {
                right: 45px;
                padding: 12px;
                gap: 10px;
            }
            
            .social-icon {
                width: 40px;
                height: 40px;
                font-size: 16px;
            }
        }

        @media (max-width: 480px) {
            .social-bookmark-tab {
                width: 38px;
                height: 65px;
                font-size: 15px;
            }
            
            .social-popup {
                right: 42px;
                padding: 10px;
                gap: 8px;
            }
            
            .social-icon {
                width: 38px;
                height: 38px;
                font-size: 15px;
            }
        }

        /* Subtle animation for bookmark */
        @keyframes bookmarkGlow {
            0%, 100% { box-shadow: -3px 0 15px rgba(0, 0, 0, 0.2); }
            50% { box-shadow: -3px 0 20px rgba(102, 126, 234, 0.4); }
        }

        .social-bookmark-tab {
            animation: bookmarkGlow 4s infinite;
        }
    </style>

    <script>
        function toggleSocialIcons() {
            const popup = document.getElementById('socialPopup');
            popup.classList.toggle('active');
        }

        // Close when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.social-bookmark-container')) {
                const popup = document.getElementById('socialPopup');
                if (popup) {
                    popup.classList.remove('active');
                }
            }
        });

        // Close on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.getElementById('socialPopup').classList.remove('active');
            }
        });

        // Close when clicking on social icons
        document.addEventListener('DOMContentLoaded', function() {
            const socialIcons = document.querySelectorAll('.social-icon');
            socialIcons.forEach(icon => {
                icon.addEventListener('click', function() {
                    setTimeout(() => {
                        document.getElementById('socialPopup').classList.remove('active');
                    }, 200);
                });
            });
        });
    </script>
    <?php
}
?>