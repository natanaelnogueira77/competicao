<script>
    class SaveChampionship 
    {
        constructor(form) 
        {
            this.dynamicForm = App.form(form);
            this.success = null;
        }

        clean() 
        {
            this.dynamicForm.clean();
        }

        loadData(content) 
        {
            this.dynamicForm.loadData(content, 'name');
        }

        setSuccess(callback = function () {}) 
        {
            this.success = callback;
            return this;
        }

        load() 
        {
            this.dynamicForm.setSuccessCallback(this.success).apply();
        }
    }
</script>