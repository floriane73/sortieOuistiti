import Filtre from './modules/Filtre.js';
//require('../css/app.css');


window.onload = () => {
    new Filtre(document.querySelector('.js-filter'));
}
