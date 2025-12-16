// send to whatsapp
function sendToWhatsApp(){
    let number = "+6282362090168"

    let name = document.getElementById('fullname').value
    let email = document.getElementById('email').value
    let message = document.getElementById('message').value

    let url = "https://wa.me/" + number + "?text="
    + "Name : " +name+ "%0a"
    + "Email : " +email+ "%0a"
    + "Message : " +message+ "%0a%0a"

    window.open(url, '_blank').focus()

}