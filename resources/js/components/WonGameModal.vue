<template>
    <div class="modal wonmodal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">You won!</h5>
                </div>
                <div class="modal-body">
                    <p>Your result has been saved.</p>
                    <p>Please return to the main page.</p>
                </div>
                <div class="modal-footer">
                    <a class="btn btn-primary" @click="$router.go(-1)">back</a>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {

}
</script>

<style></style>