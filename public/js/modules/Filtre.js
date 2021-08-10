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
            return;
        }
        this.pagination = element.querySelector('.js-filter-pagination')
        this.sorting = element.querySelector('.js-filter-sorting')
        this.content = element.querySelector('.js-filter-content')
        this.form = element.querySelector('.js-filter-form')

        this.bindEvents()
    }

    /**
     * Ajoute le comportement des éléments
     */
    bindEvents() {
        const aListener = e => {
            if (e.target.tagName === 'A') {
                e.preventDefault()
                this.loadUrl(e.target.getAttribute('href'))
            }
        }
        this.sorting.addEventListener('click', aListener)
        this.content.addEventListener('click', aListener)
        this.pagination.addEventListener('click', aListener)
        this.form.querySelectorAll('input, select').forEach(input => {
            input.addEventListener('change', this.loadForm.bind(this))
        })
    }

    /**
     * Charge les paramètres selon l'url envoyée par le formulaire
     */
    async loadForm() {
        const data = new FormData(this.form)
        const params = new URLSearchParams()
        const url = new URL(this.form.getAttribute('action') || window.location.href)

        data.forEach((value, key) => {
            params.append(key, value)
        })
        return this.loadUrl(url.pathname + '?' + params.toString())
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