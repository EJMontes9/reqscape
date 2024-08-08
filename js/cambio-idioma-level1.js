document.addEventListener('DOMContentLoaded', function() {
    const texts = {
        en: {
            home: 'Home',
            levels: 'Levels',
            score: 'Score',
            profile: 'Profile',
            info: 'Info',
            logout: 'Logout',
            level_title: 'Level 01',
            task_text: 'YOU ARE A REQUIREMENTS ENGINEER AND YOU ARE GIVEN 2 TASKS THAT WILL HELP YOU GET PROMOTED...',
            skip_button: 'SKIP'
        },
        es: {
            home: 'Inicio',
            levels: 'Niveles',
            score: 'Puntuación',
            profile: 'Perfil',
            info: 'Información',
            logout: 'Cerrar sesión',
            level_title: 'Nivel 01',
            task_text: 'ERES UN INGENIERO DE REQUERIMIENTOS Y TE PROPONEN 2 TAREAS QUE TE AYUDARÁN A ASCENDER DE PUESTO…',
            skip_button: 'SALTAR'
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
        document.getElementById('level-title').innerText = texts[selectedLanguage].level_title;
        document.getElementById('task-text').innerText = texts[selectedLanguage].task_text;
        document.getElementById('skip-button').innerText = texts[selectedLanguage].skip_button;
    }

    // Set the initial language based on the current selection
    changeLanguage();
});
