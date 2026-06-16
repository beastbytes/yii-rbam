/**
 * GridView and ListView pagination
 *
 * If using the same action for the initial view and pagination, it is recommended to use the
 * Sec-Fetch-Mode header https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/Sec-Fetch-Mode
 * to determine type of request
 *
 * Usage: new Paginator( {string} id, {?string} selector = ".grid-view nav a, .list-view nav a" )
 */

class Paginator {
    #id = ''
    #selector = ".grid-view nav a, .list-view nav a"

    /**
     * @param {string} id - DataView container element ID
     * @param {string} selector - Pagination links selector
     */
    constructor(id, selector) {
        if (typeof id === 'undefined') {
            console.log("id must be defined")
        } else {
            this.#id = id
            if (typeof selector === 'string') {
                this.#selector = selector
            }
            this.addEventListeners()
        }
    }

    addEventListeners() {
        for (const el of document.getElementById(this.#id).querySelectorAll(this.#selector)) {
            el.addEventListener(
                "click",
                (e) => {
                    e.preventDefault()
                    this.#paginate(e)
                        .then(r => this.addEventListeners())
                }
            )
        }
    }

    async #paginate(e) {
        const container = document.getElementById(this.#id)
        const dataset = { ...container.dataset, ...e.target.dataset }
        const formData = new FormData()

        Object.entries(dataset).forEach(([key, value]) => {
            if (key !== "href") {
                formData.set(key, value)
            }
        })

        const request = new Request(
            e.target.getAttribute("href"),
            {
                method: "POST",
                body: formData
            }
        )

        const response = await fetch(request)
        container.outerHTML = await response.text()
    }
}