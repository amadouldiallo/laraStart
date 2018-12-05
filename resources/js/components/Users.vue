<template>
    <div class="container">
      <div class="row mt-5">
        <div class="col-md-6 offset-md-3">


                <div class="input-group input-group-lg ">
                    <input class="form-control form-control-navbar" v-model="search" @keyup.enter="searchit" type="search" placeholder="Search" aria-label="Search">
                    <div class="input-group-append">
                        <button class="btn btn-navbar" @click ="searchit">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                </div>


        </div>
      </div>
        <div class="row justify-content-center mt-5" v-if="$gate.isAdmin() && users.data[0]">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Users List</h3>
                <div class="card-tools">
                    <button class="btn btn-success" @click="newModal"> Add User
                        <i class="fas fa-user-plus fa-fw"></i>
                    </button>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover">
                  <tbody><tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Type</th>
                    <th>Registred at</th>
                    <th>Modify</th>
                  </tr>
                  <tr v-for="user in users.data" :key="user.id">
                    <td>{{user.id}}</td>
                    <td>{{user.name}}</td>
                    <td>{{user.email}}</td>
                    <td>{{user.type|upText}}</td>
                    <td>{{user.created_at|myDate}}</td>
                    <td>
                        <a href="#" @click="editModal(user)"><i class="fas fa-edit blue fas"></i></a>/
                        <a href="#" @click="deleteUser(user.id)"><i class="fas fa-trash red"></i></a>

                    </td>
                  </tr>

                </tbody>

                </table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                <pagination :data="users" @pagination-change-page="getResults" :show-disabled="true">
                </pagination>
              </div>
            </div>
            <!-- /.card -->
          </div>
        </div>
        <div class="row justify-content mt-5" v-if="!$gate.isAdmin() || !users.data[0]">
          <not-found></not-found>
        </div>
        <div class="modal fade" id="addNew" tabindex="-1" role="dialog" aria-labelledby="addNewTitle" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="AddNewLongTitle" v-if="!editmode">Add New</h5>
                <h5 class="modal-title" id="AddNewLongTitle" v-if="editmode">Edit User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <form @submit.prevent=" editmode? updateUser(): createUser()">
                <div class="modal-body">
                  <div class="form-group">
                    <input type="text" class="form-control" :class="{'is-invalid':form.errors.has('name')}" v-model="form.name" placeholder="Name" name="name">
                    <has-error :form="form" field ="name"></has-error>
                  </div>

                  <div class="form-group">
                    <input type="email" class="form-control" :class="{'is-invalid':form.errors.has('email')}" v-model="form.email" placeholder="Email" name="email">
                    <has-error :form="form" field ="email"></has-error>
                  </div>

                  <div class="form-group">
                    <textarea class="form-control" :class="{'is-invalid':form.errors.has('bio')}" v-model="form.bio" placeholder="Short bio for user (optional) " name="bio" id="bio"> </textarea>
                    <has-error :form="form" field ="bio"></has-error>
                  </div>
                  <div class="form-group">
                    <select class="form-control" :class="{'is-invalid':form.errors.has('type')}" v-model="form.type" name="type">
                      <option value="">Select User Role</option>
                      <option value="admin">Admin</option>
                      <option value="user">Member</option>
                      <option value="author">Author</option>

                    </select>
                    <has-error :form="form" field ="type"></has-error>
                  </div>
                  <div class="form-group">
                    <input type="password" class="form-control" :class="{'is-invalid':form.errors.has('password')}" v-model="form.password" name="password" id="password">
                    <has-error :form="form" field ="password"></has-error>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary btn-danger" data-dismiss="modal">Close</button>
                  <button type="submit" class="btn btn-primary" v-if="!editmode">Create</button>
                  <button type="submit" class="btn btn-primary" v-if="editmode">Update</button>
                </div>
              </form>
            </div>
          </div>
        </div>
    </div>
</template>

<script>
    export default {
      data(){
        return {
          editmode:false,
          users:{},
          form: new Form({
            id:'',
            name:'',
            email:'',
            password:'',
            type:'',
            bio:'',
            avatar:''
          }),
          search:''
        }
      },
      methods:{
        getResults(page = 1) {
          axios.get('api/user?page=' + page)
            .then(response => {
              this.users = response.data;
            });
        },
        searchit(){
            Fire.$emit('searching',()=>{

            })
        },
        editModal(user){
          this.editmode=true
          this.form.reset();
           $('#addNew').modal('show');
           this.form.fill(user);
        },
        newModal(){
          this.editmode=false
          this.form.reset();
           $('#addNew').modal('show');
        },
        updateUser(){
          this.$Progress.start();
          this.form.put('api/user/'+this.form.id).then(()=>{
            // success
            $('#addNew').modal('hide');
            swal('Updated','Information has been updated', 'success')
            Fire.$emit('AfterAddOrModif')
          })
          .catch(()=>{
            this.$Progress.fail()
          })
        },
        /* create user by form */
        createUser(){
          this.$Progress.start();
          this.form.post('api/user')
          .then(()=>{

            Fire.$emit('AfterAddOrModif')
            $('#addNew').modal('hide');
            toast({
              type:'success',
              title: 'User is created in successfully'
            });

            this.$Progress.finish();

          })
          .catch(()=>{
            toast({
              type: 'error',
              title: 'User not created'
            })
          })
        },
        deleteUser(id){
          swal({
              title: 'Are you sure?',
              text: "You won't be able to revert this!",
              type: 'warning',
              showCancelButton: true,
              confirmButtonColor:'#0085d6',
              cancelButtonColor:'#d33',
              confirmButtonText: "Yes, delete it!"
              }).then((result)=>{
                if(result.value){
                  this.form.delete('api/user/'+ id).then(()=>{
                      swal('Deleted','Your file has been deleted', 'success')
                      Fire.$emit('AfterAddOrModif')
                  }).catch(()=>{
                      swal('Not Deleted','Your file has not been deleted', 'error')
                    })
                }
              })
        },
        /* load user from database  */
        loadUsers(){
           if(this.$gate.isAdmin()){
             this.$Progress.start()
             axios.get('api/user').then(({data})=>(this.users= data))
             this.$Progress.finish()
          }
        }
      },
      created(){
          Fire.$on('searching',()=>{
            let query = this.search
            axios.get('api/findUser?q='+query)
            .then((data)=>{
              this.users= data.data
            })
            .catch(()=>{

            })
          })
          this.loadUsers();
          Fire.$on('AfterAddOrModif',()=>{
            this.loadUsers();
          })
          //setInterval(() => this.loadUsers() , 3000);
      },
      mounted() {
          //console.log('Component mounted.')
      }
    }
</script>
