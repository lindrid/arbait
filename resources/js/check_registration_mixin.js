export default {
    methods: {
        checkRegistrationProgress: function (pageName, userType) {
            var app = this;
            this.$axios.post('check-progress',
                {
                    page: pageName,
                    user_type: userType
                }
            ).then(function (resp) {
                var redirect = resp.data.redirect;
                var page_name = resp.data.page_name;
                var params = resp.data.params;


                if (redirect == 1) {
                    if(typeof params['phone'] === 'undefined') {
                        app.$router.push({
                            name: page_name,
                            params: {user_type: userType}
                        });
                    }
                    else {
                        app.$router.push({
                            name: page_name,
                            params: {phone: params['phone'], user_type: userType}
                        });
                    }
                }
            });
        }
    }
}