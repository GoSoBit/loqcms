
$(document).ready(function(){

  $('.spoiler-text').hide()//   
  $('.spoiler').click(function(){//
    $('.spoiler-text').hide("slow")//
    $(this).toggleClass("folded").toggleClass("unfolded").next().slideToggle()//
  })//

// <!!>
  $("#recovery").submit(function(){
    $.ajax({
      type: "POST",
      url: "mods/ajax/recovery.php",
      data: "_login="+$("#_login").val() + "&mail="+$("#mail").val() + "&rec="+$("#rec").val(),
      success: function(html){
        $("#content").html(html);
       }
    });
    return false;
  });
// <!!>
  $("#registration").submit(function(){
    $.ajax({
      type: "POST",
      url: "mods/ajax/registration.php",
      data: "_login="+$("#_login").val() 
      + "&mail="+$("#mail").val()  
      + "&_passwd="+$("#_passwd").val()
      + "&passwd2="+$("#passwd2").val()
      + "&reg="+$("#reg").val(),
      success: function(html){
        $("#regiMsg").html(html);
       }
    });
    return false;
  });
// <!!>+ "&exit="+$("#exit").val()
$("#auth").submit(function(){
    $.ajax({
      type: "POST",
      url: "mods/ajax/login.php",
      data: 
      "login="+$("#login").val() 
      + "&passwd="+$("#passwd").val(),
      success: function(html){
        $("#msg").html(html);
       }
    });
    return false; 
  });
$("#exit").click(function(){
    $.ajax({
      type: "POST",
      url: "mods/ajax/login.php",
      data: "exit="+$("#exit"),
    });
    return true; 
  });
$("#changeP").submit(function(){
    $.ajax({
      type: "POST",
      url: "mods/ajax/changepassword.php",
      data: 
      "old_pass="+$("#old_pass").val() 
      + "&pass="+$("#pass").val()
	  + "&pass2="+$("#pass2").val()
	  + "&change="+$("#change").val(),
      success: function(html){
        $("#content").html(html);
       }
    });
    return false; 
  });
});