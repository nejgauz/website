var app = new Vue({
    el: '#app',
    data: function() { return {
        formVision : 'display: none',
        isHidden : true,
        file: '',
        title: '',
        isSave: true,
        token: '',
        albumId: '',
        errorTitle: '',
        errorFile: '',
    }},
    computed: {
        url: function () {
            return '/album/add_photo/' + this.$refs.albumId.value;
        }
    },
    methods: {
        showForm: function () {
            this.formVision = 'display: block'
        },
        hideForm: function () {
            this.formVision = 'display: none';
            this.errorTitle = '';
            this.errorFile = '';
            this.file = null;
        },
        stopSubmit: function (e) {
            e.preventDefault();
        },
        handleFileUpload: function () {
            this.file = this.$refs.file.files[0];
        },
        submitFile: function () {
            this.token = this.$refs.token.value;
            let formData = new FormData();
            formData.append('my_photo[file]', this.file);
            formData.append('my_photo[title]', this.title);
            formData.append('my_photo[save]', this.isSave);
            formData.append('my_photo[_token]', this.token);

            axios.post(this.url,
                formData,
                {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                })
                .then(function (response) {
                    if (typeof response.data.errors === 'undefined' || response.data.errors.length === 0) {
                        location.reload();
                    }
                    if (typeof response.data.errors.file !== 'undefined') {
                        app.$data.errorFile = response.data.errors.file[0];
                    } else {
                        app.$data.errorFile = '';
                    }
                    if (typeof response.data.errors.title !== 'undefined') {
                        app.$data.errorTitle = response.data.errors.title[0];
                    } else {
                        app.$data.errorTitle = '';
                    }
                })
                .catch(function (error) {
                    console.log(error);
                });
        },
    }
})

