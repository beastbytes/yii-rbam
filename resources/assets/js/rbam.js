/**
 * @copyright Copyright © 2025 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

const rbam = {
    init: function() {
        for (const button of document.querySelectorAll("tbody .action .btn")) {
            button.addEventListener(
                'click',
                (e) => {
                    e.preventDefault()
                    rbam.action(e.target)
                }
            )
        }

        const allItems = document.getElementById("all_items")
        if (allItems !== null) {
            allItems.addEventListener(
                'click',
                (e) => {
                    e.preventDefault()
                    rbam.action(e.target)
                }
            )
        }
    },
    action: async function(target) {
        const container = document.getElementById("js-items")
        const dataset = container.dataset
        const formData = new FormData()

        for (const property in dataset) {
            formData.set(property, dataset[property])
        }

        if (target.dataset.hasOwnProperty('name')) {
            formData.set("name", target.dataset.name)
        }

        const request = new Request(target.dataset.href, {
            method: "POST",
            body: formData
        })

        const response = await fetch(request)
        container.innerHTML = await response.text()

        rbam.init()
    }
}

rbam.init()