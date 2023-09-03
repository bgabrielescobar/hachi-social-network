
window.onload( () => {

    const signupFormSection = document.querySelector('.signup-form-section');
    const signinFormSection = document.querySelector('.signin-form-section');
    const signupLink = document.querySelector('.signup-image-link');
    const signinLink = document.querySelector('.signin-image-link');

    signupLink.addEventListener('click', function() {
        signinFormSection.classList.remove('display-on');
        signupFormSection.classList.add('display-on');
    });

    signinLink.addEventListener('click', function() {
        signupFormSection.classList.remove('display-on');
        signinFormSection.classList.add('display-on');
    });
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