<template>
    <div class="mt-3">
        <h2>Add Todo Task</h2>
        <div class="container m-2 w-100">
            <input
                type="text"
                placeholder="Enter Item"
                class="border"
                v-model="item.name"
            />
            <button
                :class="[item.name ? 'active' : 'notactive']"
                @click="action()"
            >
                Save
            </button>
        </div>
    </div>
</template>
<script>
export default {
    data: function() {
        return {
            item: {
                id:"",
                name: ""
            }
        };
    },
    methods: {
        setTodoItem(data){
            this.item.id = data.id;
            this.item.name = data.name;
        },
        action(){
            if (this.item.id) {
                this.updateItem();
            }else{
                this.addItem();
            }
        },
        addItem() {
            if (this.item.name == "") {
                return;
            }
            axios.post("api/todo/item/store", {
                item: this.item
            })
            .then((res) => {
                if (res.data.statusCode == 200) {
                    this.item.name = "";
                    this.$parent.getItems();
                }
            })
            .catch(error => {
                console.log(error);
            });
        }
    }
};
</script>

<style scoped>
.active {
    color: white;
    background-color: blue;
}
.inactive {
    color: gray;
}
</style>