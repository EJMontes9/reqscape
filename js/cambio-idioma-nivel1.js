document.addEventListener('DOMContentLoaded', function() {
    const texts = {
        en: {
            levelTitle: 'Level 01',
            tooltipHome: 'Home',
            tooltipLevels: 'Levels',
            tooltipScore: 'Score',
            tooltipProfile: 'Profile',
            tooltipInfo: 'Info',
            tooltipLogout: 'Logout',
            ambiguousTitle: 'Ambiguous',
            requirementsTitle: 'Requirements',
            nonAmbiguousTitle: 'Not Ambiguous',
            modalTitle: 'Remember:',
            // Otros textos específicos que puedas necesitar
        },
        es: {
            levelTitle: 'Nivel 01',
            tooltipHome: 'Inicio',
            tooltipLevels: 'Niveles',
            tooltipScore: 'Puntuación',
            tooltipProfile: 'Perfil',
            tooltipInfo: 'Información',
            tooltipLogout: 'Cerrar Sesión',
            ambiguousTitle: 'Ambiguo',
            requirementsTitle: 'Requerimientos',
            nonAmbiguousTitle: 'No Ambiguo',
            modalTitle: 'Para Recordar:',
            // Otros textos específicos que puedas necesitar
        }
    };

    const languageSelector = document.getElementById('languageSelector');
    languageSelector.addEventListener('change', changeLanguage);

    function changeLanguage() {
        const selectedLanguage = languageSelector.value;
        document.getElementById('levelTitle').innerText = texts[selectedLanguage].levelTitle;
        document.getElementById('tooltipHome').innerText = texts[selectedLanguage].tooltipHome;
        document.getElementById('tooltipLevels').innerText = texts[selectedLanguage].tooltipLevels;
        document.getElementById('tooltipScore').innerText = texts[selectedLanguage].tooltipScore;
        document.getElementById('tooltipProfile').innerText = texts[selectedLanguage].tooltipProfile;
        document.getElementById('tooltipInfo').innerText = texts[selectedLanguage].tooltipInfo;
        document.getElementById('tooltipLogout').innerText = texts[selectedLanguage].tooltipLogout;
        document.getElementById('ambiguousTitle').innerText = texts[selectedLanguage].ambiguousTitle;
        document.getElementById('requirementsTitle').innerText = texts[selectedLanguage].requirementsTitle;
        document.getElementById('nonAmbiguousTitle').innerText = texts[selectedLanguage].nonAmbiguousTitle;
        document.getElementById('modalTitle').innerText = texts[selectedLanguage].modalTitle;
        // Actualiza otros textos según sea necesario
    }

    // Set the initial language
    changeLanguage();
});
