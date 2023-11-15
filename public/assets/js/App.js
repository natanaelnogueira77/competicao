class App 
{
    static load(action) 
    {
        var load_div = $(".ajax_load");
        if(action === "open") {
            load_div.fadeIn().css("display", "flex");
        } else {
            load_div.fadeOut();
        }
    }

    static form(
        elem, 
        successCallback = function () {}, 
        errorCallback = function () {},
        objectify = false
    ) 
    {
        return new DynamicForm(elem, successCallback, errorCallback, objectify);
    }

    static table(elem, urlBase) 
    {
        return new DataTable(elem, urlBase);
    }

    static showMessage(
        message = '', 
        type = 'success', 
        timeOut = 5000, 
        fadeIn = 5000, 
        fadeOut = 5000, 
        positionClass = 'toast-bottom-right'
    ) 
    {
        toastr.options.timeOut = timeOut;
        toastr.options.fadeIn = fadeIn;
        toastr.options.fadeOut = fadeOut;
        toastr.options.positionClass = positionClass;

        if(type == 'success') {
            toastr.success(message);
        } else if(type == 'error') {
            toastr.error(message);
        } else if(type == 'info') {
            toastr.info(message);
        }
    }

    static createMask(jQueryElem, mask) 
    {
        if(jQueryElem && mask) {
            jQueryElem.mask(mask).focusout(function (event) {  
                var target, data, element;  
                target = (event.currentTarget) ? event.currentTarget : event.srcElement;  
                data = target.value.replace(/\D/g, '');
                
                element = $(target);  
                element.unmask();  
                element.mask(mask);
            });
        }
    }

    static callAjax(data = {}) 
    {
        $.ajax({
            url: data.url,
            type: data.type ? data.type : "get",
            data: data.data ? data.data : {},
            dataType: data.dataType ? data.dataType : "json",
            beforeSend: function () {
                if(!data.noLoad) {
                    App.load("open");
                }
            }, 
            success: function (response) {
                if(response.message) {
                    App.showMessage(response.message[1], response.message[0]);
                }

                if(data.success) {
                    data.success(response);
                }
            }, 
            error: function (response) {
                if(response.responseJSON) {
                    if(response.responseJSON.message) {
                        App.showMessage(response.responseJSON.message[1], response.responseJSON.message[0]);
                    }

                    if(data.error) {
                        data.error(response.responseJSON);
                    }
                } else {
                    console.log('Ocorreu algum erro de servidor!');
                }
            },
            complete: function () {
                if(!data.noLoad) {
                    App.load("close");
                }
            }
        });
    }

    static setCookie(cname, cvalue, exdays) 
    {
        const d = new Date();
        d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));

        let expires = "expires="+ d.toUTCString();
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    }

    static getCookie(cname) 
    {
        let name = cname + "=";
        let decodedCookie = decodeURIComponent(document.cookie);
        let ca = decodedCookie.split(';');

        for(let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) == ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) == 0) {
                return c.substring(name.length, c.length);
            }
        }
        
        return "";
    }

    static getAddressByCEP(cep, inputs = {}, awaitText = "...") 
    {
        let url = `https://viacep.com.br/ws/${cep}/json/`;
        let xmlHttp = new XMLHttpRequest();
    
        var endereco = document.getElementById(inputs.endereco);
        var bairro = document.getElementById(inputs.bairro);
        var cidade = document.getElementById(inputs.cidade);
        var estado = document.getElementById(inputs.estado);
    
        endereco.value = awaitText;
        bairro.value = awaitText;
        cidade.value = awaitText;
        estado.value = awaitText;
    
        xmlHttp.open('GET', url);
        xmlHttp.onreadystatechange = () => {
            if(xmlHttp.readyState == 4 && xmlHttp.status == 200) {
                let dadosJSONText = xmlHttp.responseText;
                let dadosJSONObj = JSON.parse(dadosJSONText);
    
                endereco.value = dadosJSONObj.logradouro ? dadosJSONObj.logradouro : '';
                bairro.value = dadosJSONObj.bairro ? dadosJSONObj.bairro : '';
                cidade.value = dadosJSONObj.localidade ? dadosJSONObj.localidade : '';
                estado.value = dadosJSONObj.uf ? dadosJSONObj.uf : '';
            }
        }
    
        xmlHttp.send();
    }

    static copyText(sel, text) 
    {
        var copyElem = document.querySelector(sel);
        copyElem.select();
        document.execCommand("Copy");
        
        App.showMessage(text + copyElem.value, "success");
    }

    static addTextAtSelectionPosition(elem, text = "") 
    {
        var curPos = elem.selectionStart;
        var textArea = $(elem).val();
        $(elem).val(textArea.slice(0, curPos) + text + textArea.slice(curPos));
    }
}