(()=>{var e={574:e=>{"use strict";e.exports=o,e.exports.isMobile=o,e.exports.default=o;const t=/(android|bb\d+|meego).+mobile|armv7l|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series[46]0|samsungbrowser.*mobile|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i,n=/CrOS/,i=/android|ipad|playbook|silk/i;function o(e){e||(e={});let o=e.ua;if(o||"undefined"==typeof navigator||(o=navigator.userAgent),o&&o.headers&&"string"==typeof o.headers["user-agent"]&&(o=o.headers["user-agent"]),"string"!=typeof o)return!1;let a=t.test(o)&&!n.test(o)||!!e.tablet&&i.test(o);return!a&&e.tablet&&e.featureDetect&&navigator&&navigator.maxTouchPoints>1&&-1!==o.indexOf("Macintosh")&&-1!==o.indexOf("Safari")&&(a=!0),a}}},t={};function n(i){var o=t[i];if(void 0!==o)return o.exports;var a=t[i]={exports:{}};return e[i](a,a.exports,n),a.exports}!function(e){if(void 0===e.ltg){var t=function(e,t){Array.prototype.forEach.call(document.querySelectorAll(e),t)};e.ltg={},e.ltg.action=t,e.ltg.removeClass=function(e,n){t(e,(function(e){return e.classList.remove(n)}))},e.ltg.addClass=function(e,n){t(e,(function(e){return e.classList.add(n)}))},e.ltg.swap=function(e,n,i){t(e,(function(e){e.classList.remove(n),e.classList.add(i)}))}}}(window),function(e,t){var n=function(){e.scrollY>0?t.body.classList.add("scrolled"):t.body.classList.remove("scrolled")};e.addEventListener("scroll",n,!1),"loading"!==t.readyState?n():e.addEventListener("DOMContentLoaded",n,!1);var i=t.getElementById("site-header");if(lightningOpt.header_scrool&&i){var o=t.getElementById("site-header").offsetHeight,a=!1,s=!1,l=function(){var n=t.getElementById("site-header").nextElementSibling;!s&&e.scrollY>o?(t.body.classList.add("header_scrolled"),lightningOpt.add_header_offset_margin&&(n.style.marginTop=o+"px")):(t.body.classList.remove("header_scrolled"),lightningOpt.add_header_offset_margin&&(n.style.marginTop=null))},c=function(n){t.body.classList.remove("header_scrolled"),e.removeEventListener("scroll",l),!1!==a&&clearTimeout(a),s=!0,a=setTimeout((function(){e.addEventListener("scroll",l,!0),s=!1}),2e3)};t.addEventListener("readystatechange",(function(){if("complete"===t.readyState){var e=t.getElementById("site-header").nextElementSibling;Array.prototype.forEach.call(t.getElementsByTagName("a"),(function(t){var n=t.getAttribute("href");"#top"===n&&t.addEventListener("click",(function(){e.style.marginTop=null})),n&&-1!==n.indexOf("#")&&(["tab"].indexOf(t.getAttribute("role"))>0||t.getAttribute("data-toggle")||t.getAttribute("carousel-control")||t.addEventListener("click",c))}))}})),t.addEventListener("DOMContentLoaded",(function(){location.hash?(e.removeEventListener("scroll",l,!1),setTimeout((function(){e.addEventListener("scroll",l,!1)}),500)):e.addEventListener("scroll",l,!1)}))}function r(){Array.prototype.forEach.call(t.getElementsByTagName("iframe"),(function(e){var t=e.getAttribute("src");if(t&&(t.indexOf("youtube")>=0||t.indexOf("vimeo")>=0||t.indexOf("maps")>=0))if("complete"===e.contentWindow.document.readyState){var n=e.getAttribute("width"),i=e.getAttribute("height")/n,o=e.offsetWidth*i;e.style.maxWidth="100%",e.style.height=o+"px"}else e.contentWindow.document.addEventListener("DOMContentLoaded",(function(){var t=e.getAttribute("width"),n=e.getAttribute("height")/t,i=e.offsetWidth*n;e.style.maxWidth="100%",e.style.height=i+"px"}))}))}e.addEventListener("DOMContentLoaded",r);var d=!1;e.addEventListener("resize",(function(){d&&clearTimeout(d),d=setTimeout(r,200)}))}(window,document),function(e,t){function n(){var e=t.getElementsByClassName("sub-section")[0];e.style.position=null,e.style.top=null,e.style.bottom=null,e.style.left=null,e.style.right=null}function i(){var e=t.getElementById("global-nav"),n=e?e.getBoundingClientRect().bottom:0;return n<0&&(n=0),n+40}function o(){var o="top";1==t.body.classList.contains("sidebar-fix-priority-top")?o="top":1==t.body.classList.contains("sidebar-fix-priority-bottom")&&(o="bottom");var a=t.body.offsetWidth,s=t.documentElement.clientHeight;if(a<992)n();else{var l=t.getElementsByClassName("main-section")[0],c=t.getElementsByClassName("sub-section")[0],r=(c.parentNode,l.getBoundingClientRect().top+e.pageYOffset),d=l.offsetHeight,u=c.offsetHeight,f=r+u;c.style.position=null,c.style.left=null;var m=c.getBoundingClientRect().left+e.pageXOffset,v=s-i();"bottom"===o&&v>u&&(o="top");var p=r+d,g=p-s,b=d-u,y=f-s,h=r-i(),L=s-(p-e.pageYOffset);if(u>d)return;var E=!1;h<e.pageYOffset&&(E=!0);var x=!1;i()+u>l.getBoundingClientRect().bottom&&(x=!0);var k=!1;y<e.pageYOffset&&(k=!0);var O=!1;g<e.pageYOffset&&(O=!0),"top"===o?E?(c.style.position="fixed",c.style.top=i()+"px",c.style.left=m+"px",x&&(c.style.position=null,c.style.left=null,c.style.top=b+"px")):n():k?(c.style.position="fixed",c.style.bottom="5px",c.style.left=m+"px",O&&(c.style.top=null,c.style.bottom=L+5+"px")):n()}}e.addEventListener("scroll",(function(){t.body.classList.contains("sidebar-fix")&&(t.getElementsByClassName("sub-section").length<1||o())})),e.addEventListener("resize",(function(){t.body.classList.contains("sidebar-fix")&&(t.getElementsByClassName("sub-section").length<1||o())}))}(window,document),(()=>{!function(e,t){function n(e,n){Array.prototype.forEach.call(t.querySelectorAll(e),n)}function i(e,t){n(e,(function(e){return e.classList.remove(t)}))}function o(e,t){n(e,(function(e){return e.classList.add(t)}))}!function(e,t,i){function a(e){var n=t.getElementById("vk-mobile-nav-menu-btn");n&&n.classList.remove("menu-open");var i=t.getElementById("vk-mobile-nav");i&&i.classList.remove("vk-mobile-nav-open")}function s(e){var i=t.getElementById("vk-mobile-nav-menu-btn");i&&i.addEventListener("click",(function(){i.classList.contains("menu-open")?a():(o(e,"menu-open"),t.getElementById("vk-mobile-nav").classList.add("vk-mobile-nav-open"))})),n(".vk-mobile-nav li > a",(function(e){e.addEventListener("click",(function(e){e.target.getAttribute("href").indexOf(!1)&&a()}))}))}"loading"===t.readyState?t.addEventListener("DOMContentLoaded",(function(){s(i)})):s(i)}(0,t,".vk-mobile-nav-menu-btn"),function(a){function s(){e.innerWidth<=5e3?(l(),o("ul.vk-menu-acc","vk-menu-acc-active"),n("ul.vk-menu-acc ul.sub-menu",(function(e){var n=t.createElement("span");n.classList.add("acc-btn","acc-btn-open"),n.addEventListener("click",c),e.parentNode.insertBefore(n,e),e.classList.add("acc-child-close")}))):l()}function l(){i("ul.vk-menu-acc","vk-menu-acc-active"),i("ul.vk-menu-acc li","acc-parent-open"),n("ul.vk-menu-acc li .acc-btn",(function(e){return e.remove()})),i("ul.vk-menu-acc li .acc-child-close","acc-child-close"),i("ul.vk-menu-acc li .acc-child-open","acc-child-open")}function c(e){var t=e.target,n=t.parentNode,i=t.nextSibling;t.classList.contains("acc-btn-open")?(n.classList.add("acc-parent-open"),t.classList.remove("acc-btn-open"),t.classList.add("acc-btn-close"),i.classList.remove("acc-child-close"),i.classList.add("acc-child-open")):(n.classList.remove("acc-parent-open"),t.classList.remove("acc-btn-close"),t.classList.add("acc-btn-open"),i.classList.remove("acc-child-open"),i.classList.add("acc-child-close"))}!function(){var n=!1,i=t.body.offsetWidth,o=function(){var e=t.body.offsetWidth;(e<i-8||i+8<e)&&(s(),i=e)};e.addEventListener("resize",(function(){!1!==n&&clearTimeout(n),n=setTimeout(o,500)}))}(),"loading"===t.readyState?t.addEventListener("DOMContentLoaded",s):s()}()}(window,document);var e=n(574);!function(t){var n=function(){var n=e.isMobile({tablet:!0});["device-mobile","device-pc"].forEach((function(e){return t.body.classList.remove(e)})),t.body.classList.add(n?"device-mobile":"device-pc")};"loading"===t.readyState?t.addEventListener("DOMContentLoaded",n):n()}(document)})()})();