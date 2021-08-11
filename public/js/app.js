import Filtre from './modules/Filtre.js';
//import AjoutLieu from './modules/AjoutLieu.js';
//require('../css/app.css');


window.onload = () => {
    new Filtre(document.querySelector('.js-filter'));
    //new AjoutLieu(document.querySelector('.js-lieu'));  pas terminé
}
