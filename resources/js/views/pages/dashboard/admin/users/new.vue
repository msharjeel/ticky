<template>
    <main class="flex-1 relative overflow-y-auto py-6 focus:outline-none" tabindex="0">
        <form @submit.prevent="saveUser">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 px-5">
                <div class="md:flex md:items-center md:justify-between">
                    <div class="flex-1 min-w-0">
                        <h1 class="py-0.5 text-2xl font-semibold text-gray-900">{{ $t('Create user') }}</h1>
                    </div>
                </div>
            </div>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mt-6 shadow sm:rounded-lg">
                    <loading :status="loading"/>
                    <div class="bg-white md:grid md:grid-cols-3 md:gap-6 px-4 py-5 sm:p-6">
                        <div class="md:col-span-1">
                            <h3 class="text-lg font-medium leading-6 text-gray-900">{{ $t('User details') }}</h3>
                            <p class="mt-1 text-sm leading-5 text-gray-500">
                                {{ $t('This information will be displayed publicly') }}.
                            </p>
                        </div>
                        <div class="mt-5 md:mt-0 md:col-span-2">
                            <div class="grid grid-cols-3 gap-6">
                                <div class="col-span-3">
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <label class="mr-2 text-sm font-medium leading-5 text-gray-700" for="name">{{ $t('User Of') }}:</label>
                                        <template
                                          v-for="user_from_options_row in user_from_options"
                                        >
                                            <span>
                                                {{ user_from_options_row.value1 }}
                                                <input
                                                    type="radio"
                                                    name="user_from"

                                                    v-model="user.user_from"
                                                    :value="user_from_options_row.key1"
                                                    
                                                    :checked="user_from_options_row.key1 == user.user_from ? 'checked' : ''"
                                                />
                                            </span>
                                        </template>
                                    </div>
                                </div>

                                <div class="col-span-3">
                                    <label class="block text-sm font-medium leading-5 text-gray-700" for="name">{{ $t('Name') }}</label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <input
                                            id="name"
                                            v-model="user.name"
                                            :placeholder="$t('Name')"
                                            class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                                            required
                                        >
                                    </div>
                                </div>
                                <div class="col-span-3">
                                    <label class="block text-sm font-medium leading-5 text-gray-700" for="email">{{ $t('Email') }}</label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <input
                                            id="email"
                                            v-model="user.email"
                                            :placeholder="$t('Email')"
                                            class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                                            required
                                            type="email"
                                        >
                                    </div>
                                </div>

                                <!-- mobile Phone number field | start -->
                                <div class="col-span-3">
                                    <label class="block text-sm font-medium leading-5 text-gray-700" for="email">{{ $t('Official mobile number') }}</label>
                                    <div class="mt-1 relative rounded-md shadow-sm">

                                        <input
                                            type="text"

                                            id="mobile_number"
                                            v-model="user.mobile_number"
                                            :placeholder="$t('mobile number')"
                                            class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                                            
                                            required

                                            v-on:keypress="isAllowedInputFor('mobile_number', $event)"

                                            @paste.prevent


                                            pattern="[0-9]{9}"
                                        >
                                        <span class="after_field_small_text">Mobile number must be 9 digit number</span>

                                    </div>
                                </div>
                                <!-- mobile Phone number field | end -->

                                <div class="col-span-3">
                                    <label class="block text-sm font-medium leading-5 text-gray-700" for="password">{{ $t('Password') }}</label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <input
                                            id="password"
                                            v-model="user.password"
                                            :placeholder="$t('Password')"
                                            class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                                            required
                                            type="password"
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="md:col-span-3">
                            <div class="py-3">
                                <div class="border-t border-gray-200"></div>
                            </div>
                        </div>
                        <div class="md:col-span-1">
                            <h3 class="text-lg font-medium leading-6 text-gray-900">{{ $t('User settings') }}</h3>
                            <p class="mt-1 text-sm leading-5 text-gray-500">
                                {{ $t('User access and permission settings') }}.
                            </p>
                        </div>
                        <div class="mt-5 md:mt-0 md:col-span-2">
                            <div class="grid grid-cols-3 gap-6">
                                <div class="col-span-3">
                                    <label class="block text-sm font-medium leading-5 text-gray-700" for="role">{{ $t('User Type') }}</label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <select
                                            id="role"
                                            v-model="user.role_id"
                                            class="mt-1 block form-select w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:shadow-outline-blue focus:border-blue-300 transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                                            required
                                        >
                                            <option :value="null" disabled>{{ $t('Select an option') }}</option>
                                            <option v-for="userRole in userRoles" :value="userRole.id">{{ userRole.name }}</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-span-3">
                                    <label class="block text-sm font-medium leading-5 text-gray-700" for="department_id">{{ $t('Organization') }}</label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <select
                                            id="department_id"
                                            v-model="user.department_id"
                                            class="mt-1 block form-select w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:shadow-outline-blue focus:border-blue-300 transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                                            
                                        >
                                            <option :value="null" disabled>{{ $t('Select an option') }}</option>
                                            <option v-for="department in departments" 
                                                :value="department.id">{{ department.name }}</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-span-3">
                                    <label class="block text-sm font-medium leading-5 text-gray-700" for="status">{{ $t('Status') }}</label>
                                    <input-switch
                                        id="status"
                                        v-model="user.status"
                                        :disabled-label="$t('The user is disabled')"
                                        :enabled-label="$t('The user is activated')"
                                    ></input-switch>
                                    <div class="mt-2 relative text-xs text-gray-500">
                                        {{ $t('When the user is deactivated, the registry is created in the system, so the email is reserved, but can not login until it is activated again') }}.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-100 text-right px-4 py-3 sm:px-6">
                        <span class="inline-flex">
                            <router-link
                                class="btn btn-secondary shadow-sm rounded-md mr-4"
                                to="/dashboard/admin/users"
                            >
                                {{ $t('Cancel') }}
                            </router-link>
                            <button
                                class="btn btn-green shadow-sm rounded-md"
                                type="submit"
                            >
                                {{ $t('Create user') }}
                            </button>
                        </span>
                    </div>
                </div>
            </div>
        </form>
    </main>
</template>

<style scoped="">
.after_field_small_text{
    color: gray;
    font-size: 11px;
}
    
</style> 
<script>
export default {
    name: "new-user",
    metaInfo() {
        return {
            title: this.$i18n.t('Create user')
        }
    },
    data() {
        return {
            loading: true,
            userRoles: [],
            user: {
                name: null,
                email: null,
                role_id: null,
                status: true,
                password: null,

                mobile_number:null,
                user_from:'internal'

            },
            user_from_options:[
                {'key1':'internal', 'value1': 'Internal'},
                {'key1':'customer', 'value1': 'Customer'}
            ],

            loading_departments:true,
            departments:[],
        }
    },
    mounted() {
        this.getUserRoles();
        this.getDepartments();
    },
    methods: {
        isAllowedInputFor(field_name, e){
            let isValid = true;
            if(field_name == 'mobile_number'){
              // pattern="[0-9]{9}"
              let char = String.fromCharCode(e.keyCode); // Get the character
              if(/^[0-9]+$/.test(char)) isValid = true; // Match with regex 
              else isValid = false;


              if(isValid){
                var test = this.user.mobile_number+char;
                var pattern = /[0-9]{10}/;
                let isValid2 = test.match(pattern);
                
                // console.log('isAllowedInputFor--', test, isValid2);

                if(isValid2 != null){
                    isValid = false;
                }

              }

              if(!isValid){
                e.preventDefault(); // If not match, don't add to input text                
              }
            }

            return  isValid;
        },
        saveUser() {
            const self = this;
            self.loading = true;
            axios.post('api/dashboard/admin/users', self.user).then(function (response) {
                self.loading = false;
                self.$notify({
                    title: self.$i18n.t('Success').toString(),
                    text: self.$i18n.t('Data saved correctly').toString(),
                    type: 'success'
                });
                self.$router.push('/dashboard/admin/users/' + response.data.user.id + '/edit');
            }).catch(function () {
                self.loading = false;
            });
        },
        getUserRoles() {
            const self = this;
            self.loading = true;
            axios.get('api/dashboard/admin/users/user-roles').then(function (response) {
                self.userRoles = response.data;
                self.loading = false;
            }).catch(function () {
                self.loading = false;
            });
        },

        getDepartments(){
            const self = this;
            self.loading_departments = true;
            axios.get('api/dashboard/admin/departments').then(function (response) {
                self.loading_departments = false;
                self.departments = response.data;
            }).catch(function () {
                self.loadingloading_departments = false;
            });
        },
    }
}
</script>
