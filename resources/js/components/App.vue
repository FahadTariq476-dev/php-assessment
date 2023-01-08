<template>
    <div class="container w-100 m-auto text-center mt-3">
        <h1 class="text-danger">Assessment Todo List</h1>
        <!-- <add-item-form /> -->
        <div class="mt-3">
            <h2>Add Todo Task</h2>
            <div class="container m-2 w-100">
                <input
                    type="text"
                    placeholder="Enter Item Title"
                    class="border"
                    v-model="item.title"
                />
                <span v-if="errors.title" class="text-danger">{{ errors.title[0] }}</span><br/>
                <textarea placeholder="Enter Item Description" class="border" v-model="item.description"></textarea>
                <span v-if="errors.description" class="text-danger">{{ errors.description[0] }}</span><br/>
                <a role="button" href="javascript:void(0)" @click="action()">
                    Save
                </a>
            </div>
        </div>
        <list-view
            :items="items"
            class="text-center"
        />
    </div>
</template>

<script>
import addItemForm from "./AddTodoItem";
import listView from "./ListTodoView";

export default {
    components: {
        addItemForm,
        listView
    },

    data: function() {
        return {
            items: [],
            item: {
                id:"",
                title: "",
                description:""
            },
            errors:[]
        };
    },
    methods: {
        setTodoItem(data){
            this.item.id = data.id;
            this.item.title = data.title;
            this.item.description = data.description;
        },
        getItems() {
            axios
                .get("api/todo/items")
                .then(res => {
                    this.items = res.data.items;
                })
                .catch(error => {
                    console.log(error);
                });
        },
        action(){
            if (this.item.id) {
                this.updateItem();
            }else{
                this.addItem();
            }
        },
        updateItem(){
         axios.put('api/todo/item/'+this.item.id, {
                item: this.item
            }).then(res => {
                if (res.data.statusCode == 200) {
                    this.item.id = "";
                    this.item.title = "";
                    this.item.description = "";
                    this.getItems();
                }
            })
            .catch(error => {
                this.errors = error.response.data;
                console.log("error from axios put", errors);
            });
        },
        addItem() {
            axios.post("api/todo/item/store", {
                item: this.item
            })
            .then((res) => {
                if (res.data.statusCode == 200) {
                    this.item.title = "";
                    this.item.description = "";
                    this.getItems();
                }
            })
            .catch((error) => {
                this.errors = error.response.data;
            });
        }
    },
    mounted() {
        this.getItems();
    }
};
</script>

<style scoped></style>
