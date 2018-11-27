<template>
<main>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card card-default">
                    <div class="card-header">
                        Users
                        <button type="button" @click="showModal('user','create')" class="btn btn-secondary btn-sm">
                            <i class="fa fa-plus"></i>&nbsp;New
                        </button>
                    </div>

                    <div class="card-body">
                        <div class="form-group row">
                                <div class="col-md-8">
                                    <div class="input-group">
                                        <select class="form-control col-md-3" v-model="criteria">
                                        <option value="name">Name</option>
                                        <option value="email">Email</option>
                                        </select>
                                        <input type="text" v-model="search" @keyup.enter="listUsers(1,search,criteria)" class="form-control" placeholder="text search">
                                        <button type="submit" @click="listUsers(1,search,criteria)" class="btn btn-primary"><i class="fa fa-search"></i> Search</button>
                                    </div>
                                </div>
                        </div>
                        <table class="table table-bordered table-striped table-sm">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="user in arrayUsers" :key="user.id">
                                    <td v-text="user.name"></td>
                                    <td v-text="user.email"></td>
                                    <td>
                                        <button type="button" @click="showModal('user','update',user)" class="btn btn-warning btn-sm">
                                          <i class="fa fa-pencil"></i>
                                        </button>&nbsp;
                                        <template v-if="user.active">
                                            <button type="button" class="btn btn-danger btn-sm" @click="toggleUser(user.id)">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </template>
                                        <template v-else>
                                            <button type="button" class="btn btn-info btn-sm" @click="toggleUser(user.id)">
                                                <i class="fa fa-check-circle"></i>
                                            </button>
                                        </template>
                                    </td>
                                </tr>                                
                            </tbody>
                        </table>
                        <nav>
                            <ul class="pagination">
                                <li class="page-item" v-if="pagination.current_page > 1">
                                    <a class="page-link" href="#" @click.prevent="changePage(pagination.current_page - 1,search,criteria)">Prev</a>
                                </li>
                                <li class="page-item" v-for="page in pagesNumber" :key="page" :class="[page == isActived ? 'active' : '']">
                                    <a class="page-link" href="#" @click.prevent="changePage(page,search,criteria)" v-text="page"></a>
                                </li>
                                <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                                    <a class="page-link" href="#" @click.prevent="changePage(pagination.current_page + 1,search,criteria)">Next</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
        <div class="modal fade" tabindex="-1" :class="{'show' : modal}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
            <div class="modal-dialog modal-primary modal-lg" role="document">
                <div class="modal-content"> 
                    <div class="modal-header">
                        <h4 class="modal-title" v-text="titleModal"></h4>
                        <button type="button" class="close" @click="closeModal()" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label" for="text-input">Name</label>
                                    <div class="col-md-9">
                                        <input type="text" v-model="name" class="form-control" placeholder="input name">
                                        
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label" for="email-input">Email</label>
                                    <div class="col-md-9">
                                        <input type="email" v-model="email" class="form-control" placeholder="Input email">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label" for="password-input">Password</label>
                                    <div class="col-md-9">
                                        <input type="password" v-model="password" class="form-control" placeholder="Input password">
                                    </div>
                                </div>
                                <div v-show="errorUser" class="form-group row">
                                    <div class="col-md-9 offset-md-3 text-error">
                                        <div v-for="error in errorShowMsg" :key="error" v-text="error">

                                        </div>
                                    </div>
                                </div>

                            </form>
                     </div>
                      <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" @click="closeModal()">Cancel</button>
                            <button type="button" v-if="typeAction==1" class="btn btn-primary" @click="createUser()">Save</button>
                             <button type="button" v-if="typeAction==2" class="btn btn-primary" @click="updateUser()">Update</button>
                        </div>
                </div> 
            </div> 
        </div>
</main>
</template>

<script>
    export default {
            data (){
                return {
                    id: 0,
                    name : '',
                    email : '',
                    password : '',
                    arrayUsers:[],
                    modal : 0,
                    titleModal : '',
                    typeAction : 0,
                    errorUser : 0,
                    errorShowMsg : [],
                    pagination : {
                        'total' : 0,
                        'current_page' : 0,
                        'per_page' : 0,
                        'last_page' : 0,
                        'from' : 0,
                        'to' : 0,
                    },
                     offset : 3,
                    search : '',
                    criteria:'name'
                }
        },
         computed:{
            isActived: function(){
                return this.pagination.current_page;
            },
            //Calcula los elementos de la paginación
            pagesNumber: function() {
                if(!this.pagination.to) {
                    return [];
                }
                
                var from = this.pagination.current_page - this.offset; 
                if(from < 1) {
                    from = 1;
                }

                var to = from + (this.offset * 2); 
                if(to >= this.pagination.last_page){
                    to = this.pagination.last_page;
                }  

                var pagesArray = [];
                while(from <= to) {
                    pagesArray.push(from);
                    from++;
                }
                return pagesArray;             

            }
        },
        methods : {
            listUsers (page,search,criteria){
                let me=this;
                var url= '/admin/user?page=' + page + '&search='+ search + '&criteria='+ criteria;
                axios.get(url).then(function (response) {
                    var result= response.data;
                    me.arrayUsers = result.users.data;
                    me.pagination= result.pagination;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            changePage(page,search,criteria){
                let me = this;
                //Actualiza la página actual
                me.pagination.current_page = page;
                //Envia la petición para visualizar la data de esa página
                me.listUsers(page,search,criteria);
            },
            createUser(){
                if (this.validateUser()){
                    return;
                }
                
                let me = this;

                axios.post('/admin/user',{
                    'name': this.name,
                    'email': this.email,
                    'password': this.password
                }).then(function (response) {
                    me.closeModal();
                    me.listUsers(1,'','name');
                }).catch(function (error) {
                    me.showErrors(error);
                });
            },
            updateUser(){
               if (this.validateUser()){
                    return;
                }
                
                let me = this;

                axios.put('/admin/user/'+this.id,{
                    'name': this.name,
                    'email': this.email,
                    'password': this.password
                }).then(function (response) {
                    me.closeModal();
                    me.listUsers(1,'','name');
                }).catch(function (error) {
                     me.showErrors(error);
                }); 
            },
            toggleUser(id){

                swal({
                title: 'Are you sure?',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, do it!',
                cancelButtonText: 'No, cancel!',
                confirmButtonClass: 'btn btn-success',
                cancelButtonClass: 'btn btn-danger',
                buttonsStyling: false,
                reverseButtons: true
                }).then((result) => {
                    if (result.value) {
                        let me = this;

                        axios.put('/admin/user/'+id+'/toggle_active',{
                            'id': id
                        }).then(function (response) {
                            me.listUsers(1,'','name');
                            swal(
                            'Ready!',
                            'User is updated',
                            'success'
                            )
                        }).catch(function (error) {
                            console.log(error);
                        });
    
                        
                    } else if (
                        // Read more about handling dismissals
                        result.dismiss === swal.DismissReason.cancel
                    ) {
                        
                    }
                }) 
            },
            showErrors(error){
               
                if (error.response.status == 422) {
                        if(error.response.data.errors["name"]!=undefined && error.response.data.errors["name"][0]!=undefined){
                          this.errorShowMsg.push(error.response.data.errors["name"][0]);
                        }
                        if(error.response.data.errors["email"]!=undefined && error.response.data.errors["email"][0]!=undefined){
                          this.errorShowMsg.push(error.response.data.errors["email"][0]);
                        }
                        if(error.response.data.errors["password"]!=undefined && error.response.data.errors["password"][0]!=undefined){
                           this.errorShowMsg.push(error.response.data.errors["password"][0]);
                        }
                                                 
                }
                if (!this.errorShowMsg.length){
                    this.errorShowMsg.push('An error has occurred, try it later');
                    
                };
                this.errorUser = 1
                return this.errorUser;
            },
            validateUser(){
                this.errorUser=0;
                this.errorShowMsg =[];

                if (!this.name) this.errorShowMsg.push("Name required");
                if (!this.email) this.errorShowMsg.push("Email required");
                 if (!this.password && this.typeAction != 2) this.errorShowMsg.push("Password required");

                if (this.errorShowMsg.length) this.errorUser = 1;

                return this.errorUser;
            },
            closeModal(){
                this.modal=0;
                this.titleModal='';
                this.name='';
                this.email='';
                this.password = '';
                this.errorUser = 0;
                this.errorShowMsg = [];
            },
            showModal(model, action, data = []){
                switch(model){
                    case "user":
                    {
                        switch(action){
                            case 'create':
                            {
                                this.modal = 1;
                                this.titleModal = 'New User';
                                this.namee= '';
                                this.email = '';
                                this.password = '';
                                this.typeAction = 1;
                                break;
                            }
                            case 'update':
                            {
                                //console.log(data);
                                this.modal=1;
                                this.titleModal='Edit User';
                                this.typeAction=2;
                                this.id=data['id'];
                                this.name = data['name'];
                                this.email= data['email'];
                                this.password = '';
                                break;
                            }
                        }
                    }
                }
            }
        },
        mounted() {
            this.listUsers(1,this.search,this.criteria);
        }
    }
</script>
<style>    
    .modal-content{
        width: 100% !important;
        position: absolute !important;
    }
    .show{
        display: list-item !important;
        opacity: 1 !important;
        position: absolute !important;
        background-color: #3c29297a !important;
    }
    .text-error{
        color: red !important;
        font-weight: bold;
    }
</style>