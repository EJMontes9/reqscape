document.addEventListener('DOMContentLoaded', function() {
    const texts = {
        en: {
            informacion: 'INFORMATION',
            ambiguoHeader: 'AMBIGUOUS',
            noAmbiguoHeader: 'NOT AMBIGUOUS',
            ambiguoText: 'Refers to those requirements that can be interpreted in different ways or that are not clear in their meaning.',
            noAmbiguoText: 'It must be clear, precise, and detailed, avoiding the use of terms subject to interpretation.',
            saltar: 'SKIP',
            practica: 'Practice',
            jugar: 'Play',
            inicio: 'Home',
            niveles: 'Levels',
            scoreglobal: 'Global Score',
            perfil: 'Profile',
            info: 'Information',
            logout: 'Log Out'
        },
        es: {
            informacion: 'INFORMACIÓN',
            ambiguoHeader: 'AMBIGUO',
            noAmbiguoHeader: 'NO AMBIGUO',
            ambiguoText: 'Se refiere a aquellos requerimientos que pueden ser interpretados de diferentes maneras o que no son claros en su significado.',
            noAmbiguoText: 'Debe ser claro, preciso y detallado, evitando el uso de términos sujetos a interpretación.',
            saltar: 'SALTAR',
            practica: 'Práctica',
            jugar: 'Jugar',
            inicio: 'Inicio',
            niveles: 'Niveles',
            scoreglobal: 'Puntuación Global',
            perfil: 'Perfil',
            info: 'Información',
            logout: 'Cerrar Sesión'
        }
    };

    const languageSelector = document.getElementById('languageSelector');
    languageSelector.addEventListener('change', changeLanguage);

    function changeLanguage() {
        const selectedLanguage = languageSelector.value;
        document.getElementById('informacion').innerText = texts[selectedLanguage].informacion;
        document.getElementById('ambiguo-header').innerText = texts[selectedLanguage].ambiguoHeader;
        document.getElementById('no-ambiguo-header').innerText = texts[selectedLanguage].noAmbiguoHeader;
        document.getElementById('ambiguo-text').innerText = texts[selectedLanguage].ambiguoText;
        document.getElementById('no-ambiguo-text').innerText = texts[selectedLanguage].noAmbiguoText;
        document.getElementById('saltar').innerText = texts[selectedLanguage].saltar;
        document.getElementById('practica').innerText = texts[selectedLanguage].practica;
        document.getElementById('jugar').innerText = texts[selectedLanguage].jugar;
        document.getElementById('inicio').alt = texts[selectedLanguage].inicio;
        document.getElementById('niveles').alt = texts[selectedLanguage].niveles;
        document.getElementById('scoreglobal').alt = texts[selectedLanguage].scoreglobal;
        document.getElementById('perfil').alt = texts[selectedLanguage].perfil;
        document.getElementById('info').alt = texts[selectedLanguage].info;
        document.getElementById('logout').alt = texts[selectedLanguage].logout;
    }

    // Set the initial language
    changeLanguage();
});
