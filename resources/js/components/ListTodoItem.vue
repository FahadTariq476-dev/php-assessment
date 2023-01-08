<template>
    <li v-if="item" class="list-group-item d-flex justify-content-between w-50">
        <!-- <input
            type="checkbox"
            @change="updateCheck()"
            v-model="item.completed"
            class="mr-3"
        /> -->
        <span :class="[item.completed ? 'completed' : '', 'item']">{{
            item.name
        }}</span>
        <button class="btn-danger ml-3" @click="removeItem()">X</button>
    </li>
</template>

<script>
export default {
    props: ["item"],
    methods: {
        updateCheck() {
            axios.put(`api/todo/item/${this.item.id}`, {
                item: this.item
            })
            .then(res => {
                if (res.status == 200) {
                    this.$emit("itemchanged");
                }
            })
            .catch(error => {
                console.log("error from axios put", errors);
            });
        },
        removeItem() {
            axios.delete(`api/todo/item/${this.item.id}`)
            .then(res => {
                if (res.status == 200) {
                    this.$emit("itemchanged");
                }
            })
            .catch(error => {
                console.log("error from axios delete ", error);
            });
        }
    }
};
</script>

<style>
.completed {
    text-decoration: line-through;
    color: gray;
}
.item {
    word-break: break-all;
}
</style>