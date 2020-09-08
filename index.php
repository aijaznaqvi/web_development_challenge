<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ACME Sports</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <style>
        .modal-mask {
            position: fixed;
            z-index: 9998;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, .5);
            display: table;
            transition: opacity .3s ease;
        }

        .modal-wrapper {
            display: table-cell;
            vertical-align: middle;
        }
    </style>
</head>
<body>
<div class="container" id="app">
    <br />
    <h3 align="center">NFL Teams</h3>
    <br />
    <div class="panel panel-default">
        <div class="panel-heading">Search Data By</div>
        <div class="panel-body">
            <div class="form-group">
                <label>Select Conference</label>
                <select class="form-control input-lg" v-model="select_conference" @change="fetchAllDataConferenceDivision">
                    <option value="">Select Conference</option>
                    <option v-for="data in conference_data" :value="data">{{ data }}</option>
                </select>
            </div>
            <div class="form-group">
                <label>Select Division</label>
                <select class="form-control input-lg" v-model="select_division" @change="fetchAllDataConferenceDivision">
                    <option value="">Select Division</option>
                    <option v-for="data in division_data" :value="data">{{ data }}</option>
                </select>
            </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="row">
                <div class="col-md-6">
                    <h3 class="panel-title">NFL Team Data</h3>
                </div>
            </div>
        </div>
        <div class="panel-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <tr>
                        <th @click="sort('name')">{{ allColumns.name }}</th>
                        <th @click="sort('nickname')">{{ allColumns.nickname }}</th>
                        <th @click="sort('display_name')">{{ allColumns.display_name }}</th>
                        <th @click="sort('conference')">{{ allColumns.conference }}</th>
                        <th @click="sort('division')">{{ allColumns.division }}</th>
                        <th>Action</th>
                    </tr>
                    <tr v-for="row in allDataSorted">
                        <td>{{ row.name }}</td>
                        <td>{{ row.nickname }}</td>
                        <td>{{ row.display_name }}</td>
                        <td>{{ row.conference }}</td>
                        <td>{{ row.division }}</td>
                        <td><button type="button" name="view" class="btn btn-primary btn-xs" @click="fetchData(row.id)">View</button></td>
                    </tr>
                </table>
                <p>
                    <button @click="prevPage">Previous</button>
                    <button @click="nextPage">Next</button>
                </p>

<!--                debug: sort={{currentSort}}, dir={{currentSortDir}}, page={{currentPage}}-->
            </div>
        </div>
    </div>
    <div v-if="myModel">
        <transition name="model">
            <div class="modal-mask">
                <div class="modal-wrapper">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" @click="myModel=false"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title">{{ dynamicTitle }}</h4>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label>{{ allColumns.name }}</label>
                                    <input type="text" class="form-control" v-model="name" disabled />
                                </div>
                                <div class="form-group">
                                    <label>{{ allColumns.nickname }}</label>
                                    <input type="text" class="form-control" v-model="nickname" disabled />
                                </div>
                                <div class="form-group">
                                    <label>{{ allColumns.display_name }}</label>
                                    <input type="text" class="form-control" v-model="display_name" disabled />
                                </div>
                                <div class="form-group">
                                    <label>{{ allColumns.conference }}</label>
                                    <input type="text" class="form-control" v-model="conference" disabled />
                                </div>
                                <div class="form-group">
                                    <label>{{ allColumns.division }}</label>
                                    <input type="text" class="form-control" v-model="division" disabled />
                                </div>
                                <br />
                                <div align="center">
                                    <button type="button" class="btn btn-default btn-xs" @click="myModel=false">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </transition>
    </div>
</div>
</body>
</html>

<script>

    var application = new Vue({
        el:'#app',
        data:{
            select_conference:'',
            conference_data:'',
            select_division:'',
            division_data:'',
            allData:[],
            allColumns:'',
            myModel:false,
            dynamicTitle:'Team Details',
            currentSort:'name',
            currentSortDir:'asc',
            pageSize:10,
            currentPage:1
        },
        methods:{
            fetchConference:function(){
                axios.post("action.php", {
                    request_for:'conference'
                }).then(function(response){
                    application.conference_data = response.data;
                    application.select_conference = '';
                });
            },
            fetchDivision:function(){
                axios.post("action.php", {
                    request_for:'division'
                }).then(function(response){
                    application.division_data = response.data;
                    application.select_division = '';
                });
            },
            fetchAllData:function(){
                axios.post('action.php', {
                    action:'fetchall'
                }).then(function(response){
                    application.allColumns = response.data.results.columns;
                    application.allData = response.data.results.data.team;
                });
            },
            fetchAllDataConferenceDivision:function(){
                axios.post('action.php', {
                    action:'fetchallbyconferencedivision',
                    conference:this.select_conference,
                    division:this.select_division
                }).then(function(response){
                    // application.allColumns = response.data.results.columns;
                    application.allData = response.data;
                });
            },
            fetchData:function(id){
                axios.post('action.php', {
                    action:'fetchSingle',
                    id:id
                }).then(function(response){
                    application.name = response.data.name;
                    application.nickname = response.data.nickname;
                    application.display_name = response.data.display_name;
                    application.conference = response.data.conference;
                    application.division = response.data.division;
                    application.myModel = true;
                    application.dynamicTitle = 'Team Details';
                });
            },
            sort:function(s) {
                //if s == current sort, reverse
                if(s === this.currentSort) {
                    this.currentSortDir = this.currentSortDir==='asc'?'desc':'asc';
                }
                this.currentSort = s;
            },
            nextPage:function() {
                if((this.currentPage*this.pageSize) < this.allData.length) this.currentPage++;
            },
            prevPage:function() {
                if(this.currentPage > 1) this.currentPage--;
            }
        },
        computed:{
            allDataSorted:function() {
                return this.allData.sort((a,b) => {
                    let modifier = 1;
                if(this.currentSortDir === 'desc') modifier = -1;
                if(a[this.currentSort] < b[this.currentSort]) return -1 * modifier;
                if(a[this.currentSort] > b[this.currentSort]) return 1 * modifier;
                return 0;
            }).filter((row, index) => {
                    let start = (this.currentPage-1)*this.pageSize;
                let end = this.currentPage*this.pageSize;
                if(index >= start && index < end) return true;
            });
            }
        },
        created:function(){
            this.fetchConference();
            this.fetchDivision();
            this.fetchAllData();
        }
    });

</script>
