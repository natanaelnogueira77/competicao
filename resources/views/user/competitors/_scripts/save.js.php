<script>
    class SaveCompetitor 
    {
        constructor(
            form, 
            mediaLibrary, 
            setOnAdd = null, 
            setOnEdit = null
        ) 
        {
            this.dynamicForm = App.form(form);
            this.fileSelector = (new FileSelector(
                '#img-area', 
                mediaLibrary.setFileTypes(['jpg', 'jpeg', 'png'])
            ));

            if(setOnAdd) {
                this.fileSelector.setOnAdd(setOnAdd);
            }
            
            if(setOnEdit) {
                this.fileSelector.setOnEdit(setOnEdit);
            }

            this.success = null;
        }

        clean() 
        {
            this.dynamicForm.clean();
            this.fileSelector.cleanFiles().render();
        }

        loadData(content) 
        {
            this.dynamicForm.loadData(content, 'name');
            this.fileSelector.cleanFiles();
            if(content.img) {
                this.fileSelector.loadFiles({
                    uri: content.img,
                    url: this.fileSelector.mediaLibrary.path + '/' + content.img
                });
            }
            this.fileSelector.render();
        }

        setSuccess(callback = function () {}) 
        {
            this.success = callback;
            return this;
        }

        load() 
        {
            const object = this;
            object.dynamicForm.setBeforeAjax(function () {
                this.formData['img'] = object.fileSelector.getURIList();
                return this;
            }).setObjectify(true).setSuccessCallback(object.success).apply();
        }
    }
</script>