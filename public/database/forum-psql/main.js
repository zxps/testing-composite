function createPost(e, topicId) {
    let form = document.createElement('form');
    form.setAttribute('action', '');
    form.innerHTML = '';
    form.innerHTML += 'User: <select name="user_id"><option value="">-- select user --</option>';
    form.innerHTML += '</select><br/>';
    form.innerHTML += 'Message:<br/><textarea name="content" placeholder="Your message"></textarea>';
    form.innerHTML += '<input type="hidden" name="act" value="create_post"/>';
    form.innerHTML += '<input type="hidden" name="topic_id" value="'+topicId+'"/>';

    let usersSelect = form.getElementsByTagName('select')[0];

    let users = document.getElementById('users').getElementsByTagName('li');
    for(let k = 0; k < users.length; k++){
        let username = users[k].getAttribute('username');
        let userId = users[k].getAttribute('user-id');
        let option = document.createElement('option');
        option.setAttribute('value', userId);
        option.innerHTML = username;
        usersSelect.appendChild(option);
    }
    let button = document.createElement('input');
    button.setAttribute('type', 'submit');
    button.setAttribute('value', 'Create post');
    form.appendChild(document.createElement('br'));
    form.appendChild(button);

    e.parentNode.insertBefore(form, e.nextSibling);
}
