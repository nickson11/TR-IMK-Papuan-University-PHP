jQuery(function(s){function l(e){s(".cf-service-fields .alert").remove(),s(".cf-service-fields .well-desc").after('<div class="alert alert-error">Error: '+e+"</div>")}s(".viewLists").click(function(){var r=s(this),e=s(this).next(),t=s("#jform_service");if(formdata=s(".cf-service-fields input").add(t),!e.is(":visible")){for(var i=formdata.length-1;0<=i;i--)if(s(formdata[i]).prop("required")&&!s(formdata[i]).val()&&"jform_list"!=s(formdata[i]).attr("id")){var a=s(formdata[i]).attr("id");return void l("Please enter a valid "+s("label[for="+a+"]").text().trim())}return token=s("#adminForm input[name=task]").prev().attr("name"),s.ajax({type:"POST",url:"index.php?option=com_ajax&format=raw&plugin=ConvertForms&task=lists&"+token+"=1",headers:{"X-CSRF-Token":token},data:formdata.serialize(),beforeSend:function(){s(".cf-service-fields .alert").remove(),r.addClass("cf-working disabled")},complete:function(){r.removeClass("cf-working disabled")},success:function(t){if(t){t=s("<div/>").html(t).text();try{t=s.parseJSON(t)}catch(e){l(e+"\n"+t)}var e,i,a;t.error?l(t.error):(e=r,i=t.lists,a="",s.each(i,function(e,t){a=a+'<li><a href="#">'+t.name+' <span class="id">'+t.id+"</span></a></li>"}),$list=e.next(),$list.html(a).show(),value=e.closest("div").find("input").val(),value&&$list.find('span:contains("'+value+'")').closest("li").addClass("active"))}else l("Can't get lists")}}),!1}e.hide()}),s(document).on("click",".cflists a",function(){var e;return e=s(this),s(".cflists li").removeClass("active"),listID=e.find(".id").text(),e.closest("div").find("input").val(listID),e.parent().addClass("active"),e.closest("ul").hide(),!1})});