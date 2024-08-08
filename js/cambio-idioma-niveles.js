
document.addEventListener('DOMContentLoaded', function() {
    const texts = {
        en: {
            home: 'Home',
            levels: 'Levels',
            score: 'Score',
            profile: 'Profile',
            info: 'Info',
            logout: 'Logout',
            level_heading: 'Levels',
            back_button: 'Back'
        },
        es: {
            home: 'Inicio',
            levels: 'Niveles',
            score: 'Puntuación',
            profile: 'Perfil',
            info: 'Información',
            logout: 'Cerrar sesión',
            level_heading: 'Niveles',
            back_button: 'Volver'
        }
    };

    const languageSelector = document.getElementById('languageSelector');
    languageSelector.addEventListener('change', changeLanguage);

    function changeLanguage() {
        const selectedLanguage = languageSelector.value;

        document.getElementById('home').innerText = texts[selectedLanguage].home;
        document.getElementById('levels').innerText = texts[selectedLanguage].levels;
        document.getElementById('score').innerText = texts[selectedLanguage].score;
        document.getElementById('profile').innerText = texts[selectedLanguage].profile;
        document.getElementById('info').innerText = texts[selectedLanguage].info;
        document.getElementById('logout').innerText = texts[selectedLanguage].logout;
        document.getElementById('level_heading').innerText = texts[selectedLanguage].level_heading;
        document.getElementById('back_button').innerText = texts[selectedLanguage].back_button;
    }

    // Set the initial language based on the current selection
    changeLanguage();
});
