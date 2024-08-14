<template>
    <main class="flex-1 relative overflow-y-auto py-6 focus:outline-none" tabindex="0">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 px-5">
            <div class="md:flex md:items-center md:justify-between">
                <div class="flex-1 min-w-0">
                    <template v-if="parentLocation.id != null"> 
                        <router-link
                            :to="'/dashboard/admin/locations-of-organization/' + parentLocation.department_id"
                            class="block hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out"
                            >
                            < Back to Locations
                        </router-link>


                        <h1 class="py-0.5 text-2xl font-semibold text-gray-900"> Sub Location(s) of: "{{ parentLocation.location_name }}"</h1>
                    </template>
                    <template v-else>
                        <a href="/dashboard/admin/organizations">< Back to Organizations</a>

                        <h1 class="py-0.5 text-2xl font-semibold text-gray-900">
                            {{ $t('Location')+'s' }} 
                        </h1>
                    </template>
                </div>
                <div class="mt-4 flex md:mt-0 md:ml-4">
                    <button
                        class="btn btn-blue shadow-sm rounded-md"
                        @click="createLocation()"
                    >
                        {{ $t('Create location') }}
                    </button>
                </div>
            </div>
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="my-6 bg-white shadow overflow-hidden sm:rounded-md">
                <loading :status="loading"/>
                <template v-if="locationsList != undefined && locationsList.length > 0">
                    <ul>
                        <template v-for="(locationRow, index) in locationsList">
                            <li :class="{'border-t border-gray-200': index !== 0}">
                                <div class="px-4 py-4 flex items-center sm:px-6">
                                    <div class="min-w-0 flex-1 sm:flex sm:items-center sm:justify-between">
                                        <div>
                                            <div class="text-sm font-medium leading-5 text-gray-900 truncate">
                                                {{ locationRow.location_name }}
                                                <template v-if="locationRow.location_category > 0">
                                                    <br/> Category: {{ getCategoryName(locationRow.location_category) }}
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ml-5 flex-shrink-0">
                                        <svg-vue class="h-5 w-5 text-gray-400" icon="font-awesome.angle-right-regular"></svg-vue>
                                    </div>

                                    <router-link
                                            v-if="parent_id == null"
                                            :to="'/dashboard/admin/sub-locations-of-organization/' + locationRow.department_id+'/' + locationRow.id"
                                            class="block hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out"
                                        >Sub Locations ( {{ (locationRow.subLocations != undefined) ? locationRow.subLocations.length : '0' }} )

                                    &nbsp | &nbsp 
                                    </router-link>

                                    <button
                                        @click="editLocation(locationRow)"
                                        class="block hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out">Edit</button>
                                    
                                    &nbsp | &nbsp 

                                    <button
                                        @click="deleteLocation(locationRow)"
                                        class="block hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out">Delete</button>

                                </div>
                            </li>
                        </template>
                    </ul>
                </template>
                <template v-else-if="!loading">
                    <div class="h-full flex">
                        <div class="m-auto">
                            <div class="grid grid-cols-1 justify-items-center h-full w-full px-4 py-10">
                                <div class="flex justify-center items-center">
                                    <svg-vue class="h-full h-auto w-64 mb-12" icon="undraw.browsing"></svg-vue>
                                </div>
                                <div class="flex justify-center items-center">
                                    <div class="w-full font-semibold text-2xl">{{ $t('No records found') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>


            <!-- Add/Edit modal | start -->
            <div class="modal fade" id="addEditModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Location</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                      </div>
                      <div class="modal-body">
                        <form>
                          <div class="form-group">
                            <label for="add_edit__location_name" class="col-form-label">Location name:</label>
                            <input type="text" class="form-control" 
                                id="add_edit__location_name" 
                                v-model="addEditLocation.location_name"
                            >
                          </div>
                          
                          <div class="form-group">
                            <label for="add_edit__location_latitude" class="col-form-label">Location latitude:</label>
                            <input type="text" class="form-control" 
                                id="add_edit__location_latitude"
                                v-model="addEditLocation.location_latitude"
                            >
                          </div>

                          <div class="form-group">
                            <label for="add_edit__location_longitude" class="col-form-label">Location longitude:</label>
                            <input type="text" class="form-control" 
                                id="add_edit__location_longitude"
                                v-model="addEditLocation.location_longitude"
                            >
                          </div>

                          <div class="form-group" v-if="addEditLocation.parent_id > 0">
                            <label for="add_edit__location_category" class="col-form-label">Location category:</label>
                            <input-select
                                    id="add_edit__location_category"                                    
                                    v-model="addEditLocation.location_category"
                                    :options="categoryList"
                                    option-label="name"                                    
                            />
                          </div>

                        </form>
                      </div>
                      <div class="modal-footer">
                        <span v-html="addEditModalMessage"></span>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" @click="saveLocation()">Save</button>
                      </div>
                    </div>
                </div>
            </div>
            <!-- Add/Edit modal | end -->


        </div>
    </main>
</template>

<script>
export default {
    name: "list",
    metaInfo() {
        return {
            title: this.$i18n.t('Locations')+'s'
        }
    },
    data() {
        return {
            loading: true,
            locationsList: [],

            parentLocation:{
                id:null,
                location_name:null,
            },
            addEditLocation:{
                id: null,
                department_id: null,
                location_name: null,
                location_latitude: null,
                location_longitude: null,
                parent_id:null,
                location_category:null,
            },
            categoryList:[],
            addEditModalMessage:'',
            defaultAddEditLocation:null,

            department_id:null,
            parent_id:null,

        }
    },
    mounted() {
        const self = this;

        setTimeout(function(){ 
            self.actionsOnPageLoad();
            console.log('187--this.parent_id', self.defaultAddEditLocation);
        },50);

    },
    /*created() {
        console.log('created-',this.$route.query, this.$router.params, this.$router, this.$route.params.id);
    },
    updated() {
        console.log('updated-',this.$route.query, this.$router.params, this.$router, this.$route.params.id);

        // this.actionsOnPageLoad();
    },
    beforeRouteUpdate(to) {
        console.log('beforeRouteUpdate-',this.$route.query, this.$router.params, this.$router, this.$route.params.id);
    },*/
    watch:{
        $route (to, from){
            // console.log('route watcher-to-from', to.path, from.path);
            // if(to.path != from.path){
            //     this.getLocations();
            // }
        },

        "$route.params.department_id"(val) {

            // console.log('mounted-',this.$route.query, this.$router.params, this.$router, 
            //     'this.$route.params.department_id='+this.$route.params.department_id,
            //     'this.parent_id='+this.parent_id
            // );

            // this.actionsOnPageLoad();

        }
    },
    methods: {
        getCategoryName(location_category){
            let category_name = "";
            for(let x in this.categoryList){
                if(this.categoryList[x].id == location_category){
                   category_name = this.categoryList[x].name; 
                }
            }
            return category_name;
        },
        actionsOnPageLoad(){
            const self = this;
            this.department_id = this.$route.params.department_id;
            this.parent_id = this.$route.params.parent_id;
            

            this.addEditLocation.department_id = this.department_id;
            this.addEditLocation.parent_id = this.parent_id;

            this.defaultAddEditLocation = this.cloneTheObject(this.addEditLocation);

            this.getLocations();
            this.getParentLocations();
            this.getCategory();
            
            // console.log('235-actionsOnPageLoad-defaultAddEditLocation-',this.defaultAddEditLocation);
        },
        cloneTheObject(obj){
            let a = JSON.stringify(obj) ;
            let b = JSON.parse(a);
            
            return b;
        },
        getDefaultLocation(){
            let temp =  this.cloneTheObject(this.defaultAddEditLocation);
            // console.log('--getDefaultLocation',temp, this.defaultAddEditLocation);
            return temp;
        },
        getLocations() {
            

            let get_location_url = 'api/dashboard/admin/department_locations';
            if(this.department_id != undefined && this.department_id > 0){
                get_location_url = 'api/dashboard/admin/department_locations?department_id='+this.department_id;

                if(this.parent_id != undefined && this.parent_id > 0){
                    get_location_url += '&parent_id='+this.parent_id;
                }
            }
            else{
                window.location.href="/dashboard/admin/organizations?msg=Select department";
            }

            console.log('in getLocations get_location_url:', get_location_url);


            const self = this;
            self.loading = true;
            axios.get(get_location_url).then(function (response) {
                self.locationsList = response.data;
                self.loading = false;
            }).catch(function () {
                self.loading = false;
            });
        },
        getCategory() {
            if(this.parent_id > 0){
                let get_location_url = 'api/dashboard/admin/category/listing';
                const self = this;
                axios.get(get_location_url).then(function (response) {
                    // console.log('293--', response);
                    if(response.data.data.length > 0){
                        self.categoryList = response.data.data;
                    }                    
                }).catch(function () {
                });
            }
        },
        getParentLocations() {
            if(this.parent_id > 0){
                let get_location_url = 'api/dashboard/admin/department_locations?id='+this.parent_id;
                const self = this;
                axios.get(get_location_url).then(function (response) {
                    if(response.data.length > 0){
                        self.parentLocation = response.data[0];
                    }                    
                }).catch(function () {
                });
            }
        },
        createLocation(){
            console.log('in createLocation-locationRow');            
            this.editLocation( null );
        },
        editLocation(locationRow){
            this.addEditLocation = locationRow == null ? this.getDefaultLocation() : this.cloneTheObject(locationRow);
            console.log('in editLocation-locationRow',this.addEditLocation);

            this.showHideModalOfAddEdit(true);
        },
        showHideModalOfAddEdit(showHide){
            if(showHide){
                $('#addEditModal').modal('show');
            }else{
                $('#addEditModal').modal('hide');
            }
        },
        showMsgR(type, title, text){
            const self = this;
            try{
                self.$notify({
                    title: self.$i18n.t(title).toString(),
                    text: self.$i18n.t(text).toString(),
                    type: type//'success'
                })
            }catch(e){
                console.log('showMsgR exception', e);
            }
        },
        saveLocation(){
            const self = this;
            console.log('in saveLocation-addEditLocation:', self.addEditLocation);

             
            axios.post('api/dashboard/admin/department_locations', self.addEditLocation).then(function (response) {
                
                self.addEditModalMessage = response.data.message;
                setTimeout(function(){ self.addEditModalMessage = ''}, 2000);

                if(response.data.success == 1 && response.data.location != undefined){
                    
                    self.showHideModalOfAddEdit(false);
                    
                    if(self.addEditLocation.id > 0){
                        //edit case
                        for(let x in self.locationsList){
                            if(self.locationsList[x].id == self.addEditLocation.id){
                                self.locationsList[x] = response.data.location;
                            }
                        }
                    }
                    else{
                        //add case
                        self.locationsList = [response.data.location].concat(self.locationsList);
                    }

                    self.$notify({
                        title: self.$i18n.t('Success').toString(),
                        text: self.$i18n.t(response.data.message).toString(),
                        type: 'success'
                    })
                }

                // response.data
            }).catch(function (e) {
                console.log('exception occured while saving:', e);
            });
        },
        deleteLocation(locationRow){
            const self = this;            
            self.addEditLocation = locationRow;
            axios.post('api/dashboard/admin/department_locations_delete?id='+locationRow.id).then(function (response) {
                
                if(response.data.success == 1){
                    if(self.addEditLocation.id > 0){
                        for(let x in self.locationsList){
                            if(self.locationsList[x].id == self.addEditLocation.id){
                                self.locationsList.splice(x, 1);
                            }
                        }
                    }
                    self.showMsgR('success', 'Success', response.data.message);
                }
                else{
                    self.showMsgR('error', 'Error', 'Not delete');
                }
                

            }).catch(function (e) {
                console.log('exception occured while saving', e)
            });
        }
    },
    
}
</script>