$('#add-image').click(function (){
    //je récupère le numéro des futurs champs que je vais créer
    const index = +$('#widgets-counter').val();

    console.log(index);

    //je récupère le prototype des entrées dans la div annonce_image et id: data_prototype
    const tmpl = $('#annonce_images').data('prototype').replace(/_name_/g, index);

    //je rajoute ce code à ma div
    $('#annonce_images').append(tmpl);

    $('#widgets-counter').val(index + 1);

    //je gère le bouton supprimer
    handleDeleteButtons();

});

function handleDeleteButtons(){
    $('button[data-action="delete"]').click(function () {
        const target = this.dataset.target;

        $(target).remove();

    })
}

function  updateCounter() {
    const count = +$('#annonce_images div.form-group').length;

    $('#widgets-counter').val(count);

}

updateCounter();

handleDeleteButtons();