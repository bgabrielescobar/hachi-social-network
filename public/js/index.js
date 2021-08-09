
window.onload( () => {
    let createAccountBottom = document.querySelector('.signup-image-link');
    let signInBottom = document.querySelector('.signin-image-link');

    let signUpSection = document.querySelector('.display-off');
    let signInSection = document.querySelector('.display-block-on');

    createAccountBottom.addEventListener('click', fadeSection);

    signInBottom.addEventListener('click', fadeSection);

    function fadeSection() {
        signUpSection.classList.toggle('display-off');
        signInSection.classList.toggle('display-off');
    }

    async function insertUser() {
        alert('mamadisimo!!!!!!!!');
        let URL_USER_REGISTERR = '/register.php';
        const formData = new FormData(document.querySelector('#register-form'))
        for (var pair of formData.entries()) {
            console.log(pair[0] + ': ' + pair[1]);
        }

    // Default options are marked with *
    const response = await fetch(URL_USER_REGISTERR, {
      method: 'POST', // *GET, POST, PUT, DELETE, etc.
      mode: 'cors', // no-cors, *cors, same-origin
      cache: 'no-cache', // *default, no-cache, reload, force-cache, only-if-cached
      credentials: 'same-origin', // include, *same-origin, omit
      headers: {
        'Content-Type': 'application/json'
        // 'Content-Type': 'application/x-www-form-urlencoded',
      },
      redirect: 'follow', // manual, *follow, error
      referrerPolicy: 'no-referrer', // no-referrer, *no-referrer-when-downgrade, origin, origin-when-cross-origin, same-origin, strict-origin, strict-origin-when-cross-origin, unsafe-url
      body: JSON.stringify(data) // body data type must match "Content-Type" header
    });
    return response.json(); // parses JSON response into native JavaScript objects
  }
});