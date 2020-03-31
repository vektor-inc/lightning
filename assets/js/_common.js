;((window) => {
    if(window.ltg === undefined) {
        window.ltg = {}
        window.ltg.action = action
        window.ltg.removeClass = removeClass
        window.ltg.addClass = addClass
        window.ltg.swap = swap

        function action(query, func) {
            Array.prototype.forEach.call(
                document.querySelectorAll(query),
                func
            )
        }

        function removeClass(query, className) {
            action(query, (elem)=>elem.classList.remove(className))
        }

        function addClass(query, className) {
            action(query, (elem)=>elem.classList.add(className))
        }

        function swap(query, remover, adder) {
            action(query, (elem)=>{
                elem.classList.remove(remover)
                elem.classList.add(adder)
            })
        }
    }
})(window);