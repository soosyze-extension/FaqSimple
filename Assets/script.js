
function toogle_faq(id) {
    var el = document.getElementById(id);
    el.style.display = el.style.display === 'none'
            ? 'block'
            : 'none';
}