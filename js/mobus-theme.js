/**
 * Mobus Theme Switcher
 * This script handles the light/dark mode switching.
 * It uses localStorage to remember the user's preference even after the page is refreshed.
 */
(function () {
    // The key name for saving in browser storage
    var STORAGE_KEY = 'mobus_theme';

    // Define our two themes
    var THEMES = [
        {
            key: 'dark',
            name: 'Dark Mode',
            desc: 'Classic dark look'
        },
        {
            key: 'light-lime',
            name: 'Light Mode',
            desc: 'Clean & Greyscale look'
        }
    ];

    /**
     * Function to apply the chosen theme
     */
    function applyTheme(themeKey) {
        // Set an attribute on the <html> tag so CSS can change colors
        document.documentElement.setAttribute('data-theme', themeKey);
        
        // Save the choice in the browser's memory (localStorage)
        localStorage.setItem(STORAGE_KEY, themeKey);
        
        // Update the UI to show which theme is active
        updatePickerUI(themeKey);
    }

    /**
     * Function to build the theme switcher button on the screen
     */
    function createSwitcher() {
        // Create the main container div
        var sw = document.createElement('div');
        sw.id = 'theme-switcher';
        sw.className = 'theme-switcher';

        // Create the dropdown menu
        var dropdown = document.createElement('div');
        dropdown.className = 'theme-dropdown';

        // Add a button for each theme in our list
        THEMES.forEach(function (t) {
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'theme-option';
            btn.setAttribute('data-theme', t.key);
            
            // Asset: assets/theme_mode.png
            var iconPath = '/mobus/assets/theme_mode.png'; 
            btn.innerHTML = '<img src="' + iconPath + '" alt="mode"><div><strong>' + t.name + '</strong><br><small>' + t.desc + '</small></div>';

            // When clicked, apply this theme
            btn.addEventListener('click', function () {
                applyTheme(t.key);
                sw.classList.remove('open'); // Close the menu
            });
            dropdown.appendChild(btn);
        });

        // Create the main trigger button (the one you see first)
        var trigger = document.createElement('button');
        trigger.type = 'button';
        trigger.className = 'theme-trigger';
        
        // Asset: assets/theme.png
        var themeIconPath = '/mobus/assets/theme.png';
        trigger.innerHTML = '<img src="' + themeIconPath + '" alt="theme"> Theme';
        
        // When clicked, open/close the menu
        trigger.addEventListener('click', function (e) {
            e.stopPropagation();
            sw.classList.toggle('open');
        });

        sw.appendChild(dropdown);
        sw.appendChild(trigger);
        document.body.appendChild(sw);

        // Close menu if user clicks anywhere else on the page
        document.addEventListener('click', function (e) {
            if (!sw.contains(e.target)) {
                sw.classList.remove('open');
            }
        });
    }

    /**
     * Sync the UI buttons
     */
    function updatePickerUI(activeKey) {
        var options = document.querySelectorAll('.theme-option');
        options.forEach(function(opt) {
            if (opt.getAttribute('data-theme') === activeKey) {
                opt.classList.add('active');
            } else {
                opt.classList.remove('active');
            }
        });
    }

    /**
     * Start everything when the page loads
     */
    function init() {
        // Check if there is a saved theme, otherwise default to 'dark'
        var savedTheme = localStorage.getItem(STORAGE_KEY) || 'dark';
        
        // Apply it immediately
        document.documentElement.setAttribute('data-theme', savedTheme);
        
        // Create the UI
        createSwitcher();
        updatePickerUI(savedTheme);
    }

    // Run the init function once the page is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
