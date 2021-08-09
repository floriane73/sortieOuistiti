/**
 * @property {HTMLElement} pagination
 * @property {HTMLElement} content
 * @property {HTMLElement} sorting
 * @property {HTMLFormElement} form
 */
export default class Filtre {

    /**
     * @param {HTMLElement | null} element
     */
    constructor(element) {
        if (element === null) {
            console.log("in element = null");
            return;
        }
        console.log('construction');
    }
}
