// document.addEventListener('DOMContentLoaded', function() {
//     const originalPost = $.post;

//     $.post = function(url, data, callback, type) {
//         // For learningStatus requests, call the original post then reload the window.
//         if (typeof url === 'string' && url.includes('/learningStatus')) {
//             originalPost.apply(this, arguments);
//             window.location.reload();
//         } else {
//             return originalPost.apply(this, arguments);
//         }
//     };
// });
