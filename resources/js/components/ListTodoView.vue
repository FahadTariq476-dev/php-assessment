<template>
    <div class="mt-4">
        <h4>Todo List</h4>
        <hr class="w-50 m-auto mb-3" />
        <table class="table">
            <thead>
                <th>#</th>
                <th>Title</th>
                <th>Description</th>
                <th>Action</th>
            </thead>
            <tbody>
                <tr
                v-for="(todo,index) in items"
                :key="todo.id">
                    <td>{{index+1}}</td>
                    <td>{{todo.title}}</td>
                    <td>{{todo.description}}</td>
                    <td>
                        <a @click="setItem(todo)" data-toggle="modal" data-target="#exampleModal" class="btn btn-primary">Edit</a>
                        <a @click="removeItem(todo.id)" class="btn btn-danger">Delete</a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
import listItem from "./ListTodoItem";
export default {
    components: {
        listItem
    },
    props: ["items"],
    methods: {
        setItem(item){
            this.$parent.setTodoItem(item);
        },
        removeItem(id) {
            axios.delete("api/todo/item/"+id)
            .then((res) => {
                if (res.data.statusCode == 200) {
                    this.$parent.getItems();
                }
            })
            .catch(error => {
                console.log("error from axios delete ", error);
            });
        }
    }
};
</script>

<style scoped></style>
