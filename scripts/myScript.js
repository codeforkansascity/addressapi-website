//these functions change the wording in the form and text input to what you are searching for. 
$("#byAddress").click(function(){
    $("#formTitle").html("Type in Address")
     $("#focusedInput").attr("placeholder", "1000 Broadway Ave Kansas City, MO")

    
});

$("#byAPN").click(function(){
    $("#formTitle").html("Type in APN")
    $("#focusedInput").attr("placeholder", "JA29310290900000000")    


    
});

$("#byKIVA").click(function(){
    $("#formTitle").html("Type in KIVA")
    $("#focusedInput").attr("placeholder", "12345") 

    
});

$("#byNhood").click(function(){
     $("#formTitle").html("Type in Neighborhood")
    $("#focusedInput").attr("placeholder", "Oak Park West")

    
});
    