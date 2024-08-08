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
            skip_button: 'SKIP',
            create_room: 'Create Room',
            practice: 'Practice',
            play: 'Play',
            room_level: 'Room Level',
            generate_code: 'Generate Code',
            start_game: 'Start Game',
            choose_option: 'Choose an option',
            solo_mode: 'Solo Mode',
            room: 'Room',
            level1: 'Level 1',
            level2: 'Level 2'
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
            skip_button: 'SALTAR',
            create_room: 'Crear Sala',
            practice: 'Práctica',
            play: 'Jugar',
            room_level: 'Nivel de Sala',
            generate_code: 'Generar Código',
            start_game: 'Comenzar Partida',
            choose_option: 'Elige una opción',
            solo_mode: 'Modo Solitario',
            room: 'Sala',
            level1: 'Nivel 1',
            level2: 'Nivel 2'
        }
    };

    const languageSelector = document.getElementById('languageSelector');
    languageSelector.addEventListener('change', changeLanguage);

    function changeLanguage() {
        const selectedLanguage = languageSelector.value;

        document.querySelectorAll('[data-translate]').forEach(element => {
            const key = element.getAttribute('data-translate');
            if (texts[selectedLanguage][key]) {
                element.innerText = texts[selectedLanguage][key];
            }
        });
    }

    // Set the initial language based on the current selection
    changeLanguage();
});
