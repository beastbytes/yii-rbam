/**
 * @copyright Copyright © 2023 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

const rbam = {
    items: null,
    init: function() {
        const itemList = document.getElementById("js-items")
        const config = {
            csrf: itemList.getAttribute("data-csrf"),
            item: itemList.getAttribute("data-item"),
            checkedUrl: itemList.getAttribute("data-checked_url"),
            uncheckedUrl: itemList.getAttribute("data-unchecked_url")
        }

        rbam.items = document.querySelectorAll("#items input")

        for (const item of rbam.items) {
            item.onclick = (e) => {
                rbam.action(e.target, config)
            }
        }

        document.getElementById("all_items").onclick = (e) => {
            rbam.all(e.target, config)
        }
    },
    action: function(target, config) {
        const formData = new FormData()

        formData.set("_csrf", config.csrf)
        formData.set("name", target.name)
        formData.set("item", config.item)

        const request = new Request(target.checked ? config.checkedUrl: config.uncheckedUrl, {
            method: "POST",
            body: formData
        })

        fetch(request)
    },
    all: function(target, config) {
        const formData = new FormData()

        formData.set("_csrf", config.csrf)
        formData.set("item", config.item)

        const request = new Request(target.getAttribute("data-url"), {
            method: "POST",
            body: formData
        })

        fetch(request)
            .then(r => {
                for (const item of rbam.items) {
                    item.checked = false
                }
            })
    }
}

rbam.init()
