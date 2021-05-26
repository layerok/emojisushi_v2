if(!themeExists()) {
    localStorage.theme = 'dark';
}

// On page load or when changing themes, best to add inline in `head` to avoid FOUC
if (localStorage.theme === 'dark') {
    setDarkTheme();
} else {
    setLightTheme()
}

function themeExists() {
    return propertyExists('theme')
}

function propertyExists(prop) {
    return (prop in localStorage);
}

function isDarkThemePreferred() {
    return (themeExists() && window.matchMedia('(prefers-color-scheme: dark)').matches)
}

function setLightTheme() {
    document.documentElement.classList.remove('dark')
    localStorage.theme = 'light';
}

function setDarkTheme() {
    document.documentElement.classList.add('dark')
    localStorage.theme = 'dark';
}

function toggleTheme(currentTheme) {

    if (currentTheme === 'dark') {
        setLightTheme();
    } else {
        setDarkTheme();
    }
}

$(document).ready(function() {
    $('#toggleDarkMode').click(function() {
        toggleTheme(localStorage.theme);
    })
})
