function sendRequest(url, value) {
    xhr = new XMLHttpRequest();
    xhr.open("POST", url);
    xhr.setRequestHeader("X-AIO-Key", "aio_AgFi49NCd2SOAbUlh8LiCrbjhbAG");
    xhr.setRequestHeader("Content-Type", "multipart/form-data");
    xhr.send(`value=${value}`);

    console.log(xhr.getResponseHeader("Content-Type"));

    xhr.onreadystatechange = (e) => {
      if (xhr.readyState !== 4) {
        return;
    }

    if (xhr.status === 200) {
        console.log('SUCCESS', xhr.responseText);
    } else {
        console.warn(xhr.response);
    }
};

}

if (sessionStorage.getItem('email') != null) {
    $(".g-signin2").hide();
    $("#signout").show();
    $("#submit_user").show();
} else {
    $("#submit_user").hide();
}

function windowSize() {
    if(window.location.href.includes('http://303.itpwebdev.com/~sauer/final_project/user.php')) {
        if ($( window ).width() < 767) {
            $(".non-mobile-row").removeClass('row justify-content-center align-items-center');
            $(".mobile-row").addClass('row justify-content-center align-items-center');
        } else {
           $(".non-mobile-row").addClass('row justify-content-center align-items-center');
           $(".mobile-row").removeClass('row justify-content-center align-items-center');     
       }
   }
   if(window.location.href.includes('household.php')) {
    if ($( window ).width() < 767) {
        $(".delete-house").html("Delete");
        $(".mobile-remove").hide();
        $(".mobile-show").show();
    }else {
        $(".mobile-remove").show();
        $(".mobile-show").hide();
        $(".delete-house").html("Delete Household");
    }
}
if(window.location.href.includes('edit_device.php') || window.location.href.includes('devices.php')) {
    if ($( window ).width() < 767) {
        $(".mobile-remove").hide();
        $(".mobile-show").show();
        $('.input-cols').removeClass('col-3');
        $('.input-row').removeClass('row justify-content-center align-items-center')
        $('.input-cols').addClass('row justify-content-center align-items-center');
        $('.input-cols').addClass('col-12');
        $('device-name').attr('text-overflow', 'ellipsis');
    } else {
        $('device-name').removeAttr('text-overflow');
        $(".mobile-remove").show();
        $(".mobile-show").hide();
        $('.input-cols').removeClass('col-12');
        $('.input-cols').removeClass('row justify-content-center align-items-center');
        $('.input-cols').addClass('col-3');
        $('.input-row').addClass('row justify-content-center align-items-center')
    }
}

}

window.addEventListener('resize', function(){
    windowSize();
});

$(window).load(function() {
    $('.carousel').carousel();
    windowSize();

    if (document.querySelectorAll("#command-form") != null) {
        document.querySelectorAll("#command-form").forEach(function(element) {
          element.addEventListener("submit",function(e) {
            e.preventDefault();
            console.log(element);
            sendRequest(element.action, element["command"].value+":"+element["value"].value);
            location.reload();
        });
      });
    }
    $("#routine-link").attr('href', 'coming_soon.php');
    if (sessionStorage.getItem('email') != null) {
        $(".g-signin2").hide();
        $("#signout").show();
        $("#submit_user").show();
    } else {
        $(".g-signin2").show();
        $("#signout").hide();
        $("#submit_user").hide();
        if (!('http://303.itpwebdev.com/~sauer/final_project/' == window.location.href || window.location.href.includes('index.php')|| window.location.href.includes('logout.php') || window.location.href.includes('login.php') || window.location.href.includes('delete_user.php') || window.location.href.includes('login.php'))) {
            alert('Please log in before continuing to this page!');
            window.location.replace("index.php");

        }
    }
});

function onSignIn(googleUser) {
    if ((typeof sessionStorage['email']  == 'undefined')) {
        var profile = googleUser.getBasicProfile();
        sessionStorage.setItem('email', 'temp');
        sessionStorage.setItem('id', 'temp');
        var url = 'login.php';
        var form = $('<form action="' + url + '" method="post">' +
          '<input style="display:none;" type="text" name="email" value="' + profile.getEmail() + '" />' +
          '<input style="display:none;" type="text" name="id" value="' + profile.getId() + '" />' +
          '<input style="display:none;" type="text" name="name" value="' + profile.getName() + '" />' +
          '<input style="display:none;" type="text" name="avatar-url" value="' + profile.getImageUrl() + '" />' +
          '</form>');
        $('body').append(form);
        form.submit();
    }
};

window.onbeforeunload = function(e){
  gapi.auth2.getAuthInstance().signOut();
};

function signOut() {
    if ((typeof sessionStorage['email']  != 'undefined')) {

        sessionStorage.clear();
        $(".g-signin2").show();
        $("#signout").hide();
        $("#submit_user").hide();
        var xhr = new XMLHttpRequest();
        window.location.replace("logout.php");
    }
}

function submitUserForm() {
    document.getElementById('user-form').submit();
}

$("#signout").on("click", function() {
    signOut();
});
