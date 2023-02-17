<style>
    @import "/static/mediate/css/chat-index.css";
</style>
<template>
    <view>
        <view class="chatInterface b-t">
            <view class="chat-container">
                <view class="chat-container-scroll" ref="myScroll">
                    <view class="clear" v-for="(message, key) in chatMessages || []" :key="key" :class="message.is_user ? 'self chat-scroll__item' : 'chat-scroll__item'">
                        <view :class="friend.online ? 'chat-scroll__item__avatar' : 'chat-scroll__item__avatar offline-gray'" v-if="!message.is_user">
                            <image :src="friend.avatar"></image>
                        </view>
                        <view class="chat-scroll__item__avatar" v-else>
                            <image :src="currentUser.avatar"></image>
                        </view>
                        <view class="chat-scroll__item__message">
                            <view class="previewImg" v-html="message.content" @click.stop="previewImg($event, message.content)"></view>
                        </view>
                    </view>
                </view>
            </view>
            <view class="send-box">
                <textarea class="send-box__input" placeholder="请输入内容" auto-height v-model="content" maxlength="-1" />
                <span class="send-box__btn" @click="sendMessage">发送</span>
                <span class="send-box__photo" @click="sendPhoto">+</span>
            </view>
        </view>
    </view>
</template>

<script>
    import myFoot from "./footer.vue"

    export default {
        components: {
            myFoot
        },
        data() {
            return {
                WebSocketUrl: {
                    "development": "ws://192.168.1.99:8111",
                    "production": "wss://yc.28888753.cn/wss",
                },
                service_id: 'sid_0',
                content: '',
                friend: {
                    username: "",
                    avatar: "",
                    online: !1,
                },
                currentUser: {
                    username: '',
                    avatar: ''
                },
                chatMessages: [],
                ws: null,
                wsIsClose: !1,  //0
                uid: '',
            }
        },
        onLoad(option) {
            this.uid = 'uid_' + uni.getStorageSync('user_id');
            this.getChat();
            this.connect();
            this.scrollToBottom();
        },
        onUnload() {
            this.wsIsClose = !0   //1，离开后关闭
            this.ws && this.ws.close();
        },
        watch: {
            chatMessages() { //监听消息，滚动到对应位置
                this.$nextTick(() => {
                    this.$refs.myScroll.scrollTop = this.$refs.myScroll.scrollHeight;
                })
            }
        },
        mounted() {
            window.onresize = () => {
                return (() => {
                    uni.pageScrollTo({
                        scrollTop: 99999,
                        duration: 0
                    })
                })()
            }
        },
        methods: {
            // 连接服务端
            connect: function() {
                var that = this;
                // 创建websocket
                that.ws = new WebSocket(that.WebSocketUrl[process.env.NODE_ENV]);
                that.ws.onopen = function() {
                    that.ws.send('{"type":"bind","uid":"' + that.uid + '"}');
                }
                that.ws.onmessage = function(e) {
                    var data = JSON.parse(e.data);
                    switch (data['type']) {
                        // 消息
                        case 'msg':
                            that.pushChat(data);
                            break;
                            // case 'receive':
                            //     that.service_id = data['server'];
                            //     break;
                    }
                }
                that.ws.onclose = function(e) {
                    if (!that.wsIsClose) {    // 1
                        that.connect();
                    }
                }
            },
            //拉取数据
            getChat: function() {
                let that = this;
                that.$http.get('index/chat', {}, result => {
                    var staff_id = result.data.chat.slice(-1)[0].staff_id;
                    if (staff_id) {
                        that.service_id = "sid_" + staff_id;
                    }
                    if (result.code == 1) {
                        //接收数据
                        let data = result.data.chat;
                        that.friend = {
                            username: '客服',
                            avatar: '/static/mediate/images/kefu.jpg',
                            online: !0,
                        }
                        that.currentUser = {
                            username: result.data.info.username,
                            avatar: result.data.info.avatar ? result.data.info.avatar : '/static/mediate/images/avatar.png'
                        }
                        that.chatMessages = data;
                    }
                })
            },
            pushChat(data) {
                this.chatMessages.push(data);
                this.scrollToBottom();
            },
            sendMessage: function() { //发送消息
                if (this.content.trim() != "") {
                    let message = {
                        type: 'msg',
                        content: this.content,
                        is_user: 1,
                        to_id: this.service_id,
                        from: this.uid,
                    };
                    this.addChat(message);
                }
                this.content = "";
            },
            addChat: function(message) {
                let that = this;
                that.$http.post('index/addchat', message, result => {
                    if (result.code == 1) {
                        that.pushChat(message);
                        that.ws.send(JSON.stringify(message));
                    }
                })
            },
            scrollToBottom() {
                this.$nextTick(function() {
                    setTimeout(() => {
                        uni.pageScrollTo({
                            scrollTop: 99999,
                            duration: 0
                        })
                    }, 300) // 要加点延迟, 不然有可能不生效
                });
            },
            sendPhoto() {
                let photo_array = [];
                var _this = this;
                uni.chooseImage({
                    count: 9, //默认9
                    sizeType: ['original', 'compressed'], //可以指定是原图还是压缩图，默认二者都有
                    sourceType: ['album'], //从相册选择
                    success: function(resa) {
                        resa.tempFilePaths.forEach((val, ind) => {
                            _this.$http.upload('index/upload', val, {}, resb => {
                                const url = resb.data.url; //上传文件位置	
                                let message = {
                                    type: 'msg',
                                    content: "<img src='" + url + "'>",
                                    is_user: 1,
                                    to_id: _this.service_id,
                                };
                                _this.addChat(message);
                            })
                        })
                    }
                });
            },
            previewImg(event, item) {
                let imgs = item.match(/<img[^>]+>/g);
                var imgArr = [];
                if (imgs) {
                    imgs.forEach(function(item, idx) {
                        item.replace(/<img[^>]*src=['"]([^'"]+)[^>]*>/gi, function(match, capture) {
                            imgArr.push(capture)
                        })
                    })
                }
                if (imgArr.length) {
                    uni.previewImage({
                        current: imgArr[0],
                        urls: imgArr,
                    });
                }
            }
        }
    }
</script>

<style>

</style>
