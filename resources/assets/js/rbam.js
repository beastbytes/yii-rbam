class Rbam {
    #id = ''

    /**
     * @param {string} id - DataView container element ID
     */
    constructor(id) {
        if (typeof id === 'undefined') {
            console.log("id must be defined")
        } else {
            this.#id = id
        }
    }

    /**
     * Fetch data from the defined URL using POST.
     * The request body is formData consisting of container and target data-* attributes;
     * target attributes take precedence.
     * The text response replaces the outerHTML of the container element and paginators have event listeners added.
     *
     * @param {{href: string, data: Object}} args
     * @returns {Promise<void>}
     */
    async action(args) {
        const container = document.getElementById(this.#id)
        const dataset = { ...container.dataset, ...args.data }
        const formData = new FormData()

        Object.entries(dataset).forEach(([key, value]) => {
            formData.set(key, value)
        })

        const request = new Request(args.href, {
            method: "POST",
            body: formData
        })

        const response = await fetch(request)
        container.outerHTML = await response.text()

        window.paginators.forEach((paginator) => {
            paginator.addEventListeners()
        })
    }
}