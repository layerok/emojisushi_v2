var My = {
    namespace: function (ns) {
        var parts = ns.split("."),
            object = this,
            i, len;
        for (i=0, len=parts.length; i < len; i++) {
            if (!object[parts[i]]) {
                object[parts[i]] = {};
            }
            object = object[parts[i]];
        }
        return object;
    }
};

My.namespace('Classes.Storage');
My.namespace('Inst.Storage');

My.Classes.Storage = function () {
    function Storage()
    {

    }

    Storage.prototype.exists = function (key) {
        return (key in localStorage);
    }

    return Storage;
}();

My.Inst.Storage = new My.Classes.Storage();




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
    return My.Inst.Storage.exists('theme')
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
