/**
 * @property {HTMLElement} content
 * @property {HTMLFormElement} form
 */
export default class AjoutLieu {

    /**
     * @param {HTMLElement | null} element
     */
    constructor(element) {
        if (element === null) {
            return;
        }
        this.content = element.querySelector('.js-lieu-content')
        this.form = element.querySelector('.js-lieu-form')
        this.bindEvents()
    }

    /**
     * Ajoute le comportement des éléments
     */
    bindEvents() {
        this.form.querySelectorAll('button').forEach(button => {
            button.addEventListener('click', this.loadForm.bind(this))
        })
    }

    /**
     * Charge les paramètres selon l'url envoyée par le formulaire
     */
    async loadForm(e) {
        e.preventDefault()
        const data = new FormData(this.form)
        const params = new URLSearchParams()
        const url = new URL(this.form.getAttribute('action') || window.location.href)
        data.forEach((value, key) => {
            params.append(key, value)
        })
        console.log(params.toString());
       // return this.loadUrl(url.pathname + '?' + params.toString())
    }

    /**
     * Charge un json à partir de l'url donnée par le formulaire
     */
    async loadUrl(url, append = false) {
        const params = new URLSearchParams(url.split('?')[1] || '')
        const response = await fetch(url.split('?')[0] + '?' + params.toString(), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }
        )
        if (response.status >= 200 && response.status < 300) {
            const data = await response.json()
            this.sorting.innerHTML = data.sorting;
            this.content.innerHTML = data.content;
            this.pagination.innerHTML = data.pagination;

            history.replaceState({}, null, url)
        } else {
            console.error(response)
        }
    }

}