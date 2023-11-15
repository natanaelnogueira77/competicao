class DynamicForm 
{
    static _defaultSuccessCallback = null;
    static _defaultErrorCallback = null;
    static _defaultActions = null;
    static _defaultAjaxParams = {};
    _successCallback = null;
    _errorCallback = null;
    _feedbackCallback = null;
    _actions = null;
    _ajaxParams = {};
    _beforeAjax = null;
    _objectify = false;
    form = null;
    formData = {};

    constructor(
        form, 
        successCallback = function () {}, 
        errorCallback = function () {}, 
        objectify = false
    ) 
    {
        this.form = form;
        this._successCallback = successCallback;
        this._errorCallback = errorCallback;
        this._objectify = objectify;
    }

    static setDefaultSuccessCallback(callback = function () {}) 
    {
        DynamicForm._defaultSuccessCallback = callback;
    }
    
    static setDefaultErrorCallback(callback = function () {}) 
    {
        DynamicForm._defaultErrorCallback = callback;
    }

    static setDefaultActions(callback = function () {}) 
    {
        DynamicForm._defaultActions = callback;
    }

    static setDefaultAjaxParams(params = {}) 
    {
        DynamicForm._defaultAjaxParams = params;
    }

    setForm(form) 
    {
        this.form = form;
        return this;
    }

    setSuccessCallback(callback = function () {}) 
    {
        this._successCallback = callback;
        return this;
    }

    setErrorCallback(callback = function () {}) 
    {
        this._errorCallback = callback;
        return this;
    }

    setFeedbackCallback(callback = function () {}) 
    {
        this._feedbackCallback = callback;
        return this;
    }

    setBeforeAjax(callback = function () {}) 
    {
        this._beforeAjax = callback;
        return this;
    }

    setActions(callback = function () {}) 
    {
        this._actions = callback;
        return this;
    }

    setAjaxParams(params = {}) 
    {
        this._ajaxParams = params;
        return this;
    }

    setObjectify(value) 
    {
        this._objectify = value;
        return this;
    }

    objectify() 
    {
        var data = {};
        var serializedData = this.form.serializeArray();
        if(serializedData) {
            for(var i = 0; i < serializedData.length; i++){
                data[serializedData[i]["name"]] = serializedData[i]["value"];
            }
        }

        return data;
    }

    clean() 
    {
        if(!this.form) {
            console.error(new Error("Form wasn't settled!"));
            return this;
        }

        this.form.find("input, textarea, select").each(function () {
            if($(this).attr("type") !== "submit" 
                && $(this).attr("type") !== "checkbox" 
                && $(this).attr("type") !== "radio") {
                $(this).val(``);
            } else if($(this).attr("type") == "checkbox" || $(this).attr("type") == "radio") {
                $(this).prop('checked', false);
            }
        });

        return this;
    }

    loadData(content = {}, attr = 'id') 
    {
        if(!this.form) {
            console.error(new Error("Form wasn't settled!"));
            return this;
        }
        
        this.form.find("input, textarea, select").each(function () {
            if($(this).attr("type") !== "submit" 
                && $(this).attr("type") !== "checkbox" 
                && $(this).attr("type") !== "radio") {
                if(content[$(this).attr(attr)] != '') {
                    $(this).val(content[$(this).attr(attr)]);
                } else {
                    $(this).val(``);
                }
            } else if($(this).attr("type") == "checkbox") {
                if(content[$(this).attr(attr)]) {
                    $(this).prop('checked', true);
                } else {
                    $(this).prop('checked', false);
                }
            } else if($(this).attr("type") == "radio") {
                if(content[$(this).attr(attr)] == $(this).val()) {
                    $(this).prop('checked', true);
                } else {
                    $(this).prop('checked', false);
                }
            }
        });

        return this;
    }

    apply() 
    {
        const object = this;
        if(DynamicForm._defaultActions) {
            DynamicForm._defaultActions(object);
        }

        if(object._actions) {
            object._actions(object);
        }

        object.form.submit(function (e) {
            e.preventDefault();

            object.formData = object._objectify ? object.objectify() : object.form.serialize();
            if(object._beforeAjax) {
                object._beforeAjax();
            }

            $.ajax({
                url: object.form.attr("action"),
                type: object.form.attr("method"),
                data: object.formData,
                dataType: 'json',
                success: function (response) {
                    DynamicForm._defaultSuccessCallback(object, response);
                    if(object._successCallback) {
                        object._successCallback(object, response);
                    }
                },
                error: function (response) {
                    if(response.responseJSON) {
                        DynamicForm._defaultErrorCallback(object, response.responseJSON);
                        if(object._errorFeedback) {
                            object._errorFeedback(object, response.responseJSON);
                        }
                    } else {
                        console.error(new Error(`The requisition of the Form returned an error! ${response}`));
                    }
                },
                ...DynamicForm._defaultAjaxParams,
                ...object._ajaxParams
            });
        });

        return object;
    }
}