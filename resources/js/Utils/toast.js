import Swal from 'sweetalert2'

const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.onmouseenter = Swal.stopTimer
        toast.onmouseleave = Swal.resumeTimer
    },
})

export const notifySuccess = (message) => {
    return Toast.fire({
        icon: 'success',
        title: message,
    })
}

export const notifyError = (message) => {
    return Toast.fire({
        icon: 'error',
        title: message,
    })
}

export const confirmDialog = async ({
    title = 'Are you sure?',
    text = "You won't be able to revert this!",
    confirmButtonText = 'Yes, delete it!',
    cancelButtonText = 'Cancel',
}) => {
    return Swal.fire({
        title,
        text,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#4361ee',
        cancelButtonColor: '#e7515a',
        confirmButtonText,
        cancelButtonText,
        reverseButtons: true,
    })
}

export default Toast
