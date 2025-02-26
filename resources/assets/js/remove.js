/**
 * @copyright Copyright © 2025 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

const remove = {
    csrf: '',
    href: '',

    init: function() {
        for (const button of document.querySelectorAll(".remove")) {
            button.addEventListener(
                "click",
                function (e) {
                    e.preventDefault()

                    remove.csrf = this.dataset.csrf
                    remove.href = this.href
                    const name = this.href
                        .split("/")
                        .pop()
                        .replaceAll("_", " ")
                        .replace(/(?:^|\s|["'([{])+\S/g, match => match.toUpperCase())
                        .replaceAll(" ", "")

                    const dialog = document.getElementById(this.dataset.openDialog)
                    const headerContent = dialog.getElementsByClassName("dialog-header-content")[0]
                    headerContent.innerHTML = headerContent.innerHTML.replaceAll("|name|", name)

                    const body = dialog.getElementsByClassName("dialog-body")[0]
                    body.innerHTML = body.innerHTML.replaceAll("|name|", name)

                    document.getElementById("remove-continue").addEventListener(
                        "click",
                        remove.remove
                    )
                }
            )
        }
    },
    remove: function () {
        const request = new Request(remove.href, {
            method: "POST",
            headers: {
                "X-CSRF-Token": remove.csrf
            }
        })

        fetch(request)
            .then(r => {
                window.location.reload()
            })
    }
}

remove.init()