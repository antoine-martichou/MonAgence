$('select').select2();

let $contactButton = $('#contactButton')
$contactButton.click(e =>{
    e.preventDefault()
    $('#contactForm').slideDown()
    $contactButton.slideUp()
})