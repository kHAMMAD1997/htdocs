// Select all elements and hide those whose id matches the pattern
document.querySelectorAll('[id^="col-dropdown-"]').forEach((el) => {
    el.style.display = 'none';
});

document.querySelector("table").style.setProperty("width", "1080px", "important");

document.querySelector("div.sub-total:last-of-type").style.setProperty("width", "1080px", "important");



