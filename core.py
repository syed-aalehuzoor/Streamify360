from include import *

client = TelegramClient('session_name', api_id, api_hash).start(bot_token=bot_token)

class ReplyMessage():
    def get_plan_message(user, request: str):
        userplan = plans[int(user['plan'])]['name'] 
        user_details = f'User ID: {user['userid']}\nName: {user['username']}\n\n'
        if request == 'plan' or request == userplan:
            return f'{user_details}💠Plan: {plans[int(user['plan'])]['name']}(Active)\n\nFeatures:\n✓ {'\n✓ '.join(feature for feature in plans[int(user['plan'])]['features'])}'

        return f'{user_details}💠Plan: {request}\n\nFeatures:\n✓ {'\n✓ '.join(feature for feature in plans[int(user['plan'])]['features'])}'

    def get_plan_buttons(userplan: str, request: str):
        global plans
        if request == 'plan':
            return [[InlineKeyboardButton(text=plan['name'], callback_data=f'planshow{plan["name"]}')] for plan in plans] + [[InlineKeyboardButton(text='<=Back', callback_data='cancel')]]
        
        back_button = [InlineKeyboardButton(text='<=Back', callback_data='back')]

        # Find indices of userplan and requested plan
        userplan_index = next((i for i, plan in enumerate(plans) if plan['name'] == userplan), None)
        requested_plan_index = next((i for i, plan in enumerate(plans) if plan['name'] == request), None)
        
        # If the request is the same as the current plan, just show the back button
        if request == userplan:
            return [back_button]

        # If the user is on the first plan, any request is an upgrade
        if userplan_index is not None and userplan_index < len(plans) - 1:
            if requested_plan_index is not None and requested_plan_index > userplan_index:
                return [[InlineKeyboardButton(text=f'Upgrade to {request}', callback_data=f'upgradeplan{request}')], back_button]

        return [back_button]

plans_buttons = []

for plan_instance in plans:
    plans_buttons.append([InlineKeyboardButton(text=f'{plan_instance['name']}', callback_data=f'planshow{plan_instance['name']}')])

mainmenu = [
    [InlineKeyboardButton(text='Transcode', callback_data='transcode')],
    [InlineKeyboardButton(text='Plan', callback_data='plan')]
    ]

class BotMap:
    def __init__(self, mainmenu):
        self.mainmenu = mainmenu

    async def main_menu(self, update: Update, context: ContextTypes.DEFAULT_TYPE):
        if update.callback_query:
            await update.callback_query.message.edit_text(text='Main Menu\n\n/plan - See your Subscription Plans\n/transcode - Transcode Video', reply_markup=InlineKeyboardMarkup(self.mainmenu))
        else:
            await context.bot.send_message(chat_id=update.effective_chat.id, text='Main Menu\n\n/plan - See your Subscription Plans\n/transcode - Transcode Video', reply_markup=InlineKeyboardMarkup(self.mainmenu))

    async def end_conversation(self, update: Update, context: ContextTypes.DEFAULT_TYPE):
        await self.main_menu(update=update, context=context)
        return ConversationHandler.END
    
    async def ask_to_end_conversation(self, update: Update, context: ContextTypes.DEFAULT_TYPE):
        await context.bot.send_message(chat_id=update.effective_chat.id, text='/cancel to go to Main Menu')

bot_map = BotMap(mainmenu=mainmenu)

class Transcoder():

    async def transcode(self, update: Update, context: ContextTypes.DEFAULT_TYPE):
        chat_id = update.effective_chat.id
        chat = await client.get_entity(chat_id)
        message = await context.bot.send_message(chat_id=chat_id, text='Waiting for Downloads')

        video_download_task = context.user_data.get('Video Download Task')
        logo_download_task = context.user_data.get('Logo Download Task')
        subtitle_download_task = context.user_data.get('Subtitle Download Task')

        if video_download_task:
            video_filepath = await video_download_task

        if logo_download_task:
            logo_filepath = await logo_download_task
        else:
            logo_filepath = None

        if subtitle_download_task:
            subtitle_filepath = await subtitle_download_task
        else:
            subtitle_filepath = None

        # Construct FFmpeg command
        if logo_filepath and subtitle_filepath:
            command = f'ffmpeg -y -i "{video_filepath}" -i "{logo_filepath}" -filter_complex "[1:v]scale=w=iw:h=ih[logo];[0:v][logo]overlay=format=auto,ass=\'{subtitle_filepath}\'" -c:v libx264 -preset veryfast -crf 22 -c:a aac -b:a 128k -metadata:s:a:0 language=ur -movflags +faststart "output_{video_filepath}"'
        elif logo_filepath:
            command = f'ffmpeg -y -i "{video_filepath}" -i "{logo_filepath}" -filter_complex "[1:v]scale=w=iw:h=ih[logo];[0:v][logo]overlay=format=auto" -c:v libx264 -preset veryfast -crf 22 -c:a aac -b:a 128k -metadata:s:a:0 language=ur -movflags +faststart "output_{video_filepath}"'
        elif subtitle_filepath:
            command = f'ffmpeg -y -i "{video_filepath}" -vf "ass=\'{subtitle_filepath}\'" -c:v libx264 -preset veryfast -crf 22 -c:a aac -b:a 128k -metadata:s:a:0 language=ur -movflags +faststart "output_{video_filepath}"'
        else:
            command = f'ffmpeg -y -i "{video_filepath}" -c:v libx264 -preset veryfast -crf 22 -c:a aac -b:a 128k -metadata:s:a:0 language=ur -movflags +faststart "output_{video_filepath}"'

        await context.bot.edit_message_text(text='Transcoding Started', chat_id=chat_id, message_id=message.id)

        # Get video duration
        cmd = f'ffprobe -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 "{video_filepath}"'
        result = subprocess.run(cmd, stdout=subprocess.PIPE, stderr=subprocess.STDOUT, shell=True)
        total_duration = float(result.stdout)

        # Start FFmpeg process
        process = subprocess.Popen(command, stderr=subprocess.PIPE, text=True)
        pattern = re.compile(r"time=(\d+:\d+:\d+\.\d+)")  # Regex pattern to capture the time
        stderror = []

        try:
            while True:
                line = process.stderr.readline()
                if not line:
                    break
                stderror.append(line)
                match = pattern.search(line)
                if match:
                    current_time = sum(x * float(t) for x, t in zip([3600, 60, 1, 0.01], match.group(1).split(':')))
                    progress = (current_time / total_duration) * 100
                    await context.bot.edit_message_text(text=f"Transcoding: {progress:.2f}%", chat_id=chat_id, message_id=message.id)
        finally:
            process.wait()
            if process.returncode != 0:
                print("FFmpeg exited with an error.")
                print(stderror)
                sys.exit(1)
            else:
                await context.bot.edit_message_text(text=f"Transcoding: 100%", chat_id=chat_id, message_id=message.id)
                if os.path.exists(f'output_{video_filepath}'):
                    await client.send_file(entity=chat, file=f'output_{video_filepath}')
                    os.remove(f'output_{video_filepath}')

        # Clean up files
        os.remove(video_filepath)
        if logo_filepath and os.path.exists(logo_filepath):
            os.remove(logo_filepath)
        if subtitle_filepath and os.path.exists(subtitle_filepath):
            os.remove(subtitle_filepath)

    async def start(self, update: Update, context:ContextTypes.DEFAULT_TYPE):
        await context.bot.send_message(chat_id=update.effective_chat.id,text='Send Video')
        return UPLOADING_VIDEO

    async def video_file(self, update: Update, context:ContextTypes.DEFAULT_TYPE):
        if user_plans.request_invalid(userid=str(update.effective_user.id),size=update.message.video.file_size):
            await update.message.reply_text(text='Whoa there!\nTrying to sneak into VIP territory? Stick to your lane, buddy! Upgrade your plan for extra perks or risk getting lost in the basic zone!\n\nUpgrade Required: Subscription Limit Exceeded\n\n/plans See More Detaile')
            return ConversationHandler.END
        userid = str(update.effective_user.id)
        chat_id = update.effective_chat.id
        chat = await client.get_entity(chat_id)
        message_id = update.effective_message.id
        message = await client.get_messages(chat_id, ids=message_id)
        msg = await client.send_message(entity=chat, message='Uploading Video')
        async def progress(current, total):
            try:
                await client.edit_message(entity=chat, message=msg, text=f'Uploading: {((current / total) * 100):.1f}%')
            except Exception as e:
                print('Exception')
        download_task = asyncio.create_task(client.download_media(message=message, file=f'{userid}.mp4', progress_callback=progress))
        await context.bot.send_message(chat_id=update.effective_chat.id, text='Send Logo File', reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton(text='Skip', callback_data='skip_logo')]]))
        context.user_data['Video Download Task'] = download_task
        return UPLOADING_LOGO

    async def skip_logo(self, update: Update, context:ContextTypes.DEFAULT_TYPE):
        await context.bot.send_message(chat_id=update.effective_chat.id,text='Send Subtitles File(.ASS)', reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton(text='Skip', callback_data='skip_subtitle')]]))
        context.user_data['Logo Download Task'] = None
        return UPLOADING_SUBTITLE

    async def logo_file(self, update: Update, context:ContextTypes.DEFAULT_TYPE):
        userid = str(update.effective_user.id)
        chat_id = update.effective_chat.id
        chat = await client.get_entity(chat_id)
        message_id = update.effective_message.id
        message = await client.get_messages(chat_id, ids=message_id)
        msg = await client.send_message(entity=chat, message='Uploading Logo')
        async def progress(current, total):
            await client.edit_message(entity=chat, message=msg, text=f'Uploading: {((current / total) * 100):.1f}%')
        download_task = asyncio.create_task(client.download_media(message=message, file=f'{userid}.png', progress_callback=progress))
        await context.bot.send_message(chat_id=update.effective_chat.id, text='Send Subtitles File(.ASS)', reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton(text='Skip', callback_data='skip_subtitle')]]))
        context.user_data['Logo Download Task'] = download_task
        return UPLOADING_SUBTITLE

    async def skip_subtitle(self, update: Update, context:ContextTypes.DEFAULT_TYPE):
        if context.user_data['Logo Download Task'] == None:
            await context.bot.send_message(chat_id=update.effective_chat.id,text="You can't Skipped Logo and Subtitles at same time.\n\nSend Logo File.", reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton(text='Skip', callback_data='skip_logo')]]))
            return UPLOADING_LOGO
        await self.transcode(update=update, context=context)
        context.user_data['Subtitle Download Task'] = None
        return ConversationHandler.END

    async def subtitle_file(self, update: Update, context:ContextTypes.DEFAULT_TYPE):
        userid = str(update.effective_user.id)
        chat_id = update.effective_chat.id
        chat = await client.get_entity(chat_id)
        message_id = update.effective_message.id
        message = await client.get_messages(chat_id, ids=message_id)
        msg = await client.send_message(entity=chat, message='Uploading Subtitles')
        async def progress(current, total):
            try:
                await client.edit_message(entity=chat, message=msg, text=f'Uploading: {((current / total) * 100):.1f}%')
            except Exception as e:
                print(e)
        download_task = asyncio.create_task(client.download_media(message=message, file=f'{userid}.ass', progress_callback=progress))
        context.user_data['Subtitle Download Task'] = download_task
        await self.transcode(update=update, context=context)
        return ConversationHandler.END

transcoder = Transcoder()

class Plan():
    async def start(update: Update, context:ContextTypes.DEFAULT_TYPE):    
        user = user_plans.get_user(userid=str(update.effective_user.id))
        await context.bot.send_message(chat_id=update.effective_chat.id, text=ReplyMessage.get_plan_message(user=user,request= 'plan'), reply_markup=InlineKeyboardMarkup(ReplyMessage.get_plan_buttons(plans[int(user['plan'])]['name'], 'plan')))
        return USER_PLAN_SUBSCRIPTION

    async def back_to_mainmenu(update: Update, context:ContextTypes.DEFAULT_TYPE):
        user = user_plans.get_user(str(update.effective_user.id))
        await context.bot.editMessageText(chat_id=update.effective_chat.id, text=ReplyMessage.get_plan_message(user=user,request= 'plan'), message_id=update.callback_query.message.message_id, reply_markup=InlineKeyboardMarkup(ReplyMessage.get_plan_buttons(plans[int(user['plan'])]['name'], 'plan')))
        return USER_PLAN_SUBSCRIPTION

    async def show_plan(update: Update, context:ContextTypes.DEFAULT_TYPE):
        user = user_plans.get_user(str(update.effective_user.id))
        requested_plan = update.callback_query.data[8:]
        await context.bot.editMessageText(chat_id=update.effective_chat.id, text=ReplyMessage.get_plan_message(user=user,request=requested_plan),message_id=update.callback_query.message.message_id, reply_markup=InlineKeyboardMarkup(ReplyMessage.get_plan_buttons(plans[int(user['plan'])]['name'], requested_plan)))
        return SHOWING_PLAN

    async def send_Invoice (update: Update, context:ContextTypes.DEFAULT_TYPE):
        user = user_plans.get_user(userid=str(update.effective_user.id))
        request = update.callback_query.data[8:]
        userplan_index = next((i for i, plan in enumerate(plans) if plan['name'] == plans[int(user['plan'])]['name']), None)
        requested_plan_index = next((i for i, plan in enumerate(plans) if plan['name'] == request), None)

        if userplan_index is not None and userplan_index < len(plans) - 1:
            if requested_plan_index is not None and requested_plan_index > userplan_index:
                requested_plan = plans[requested_plan_index]
                await context.bot.send_invoice(
                    chat_id=update.effective_chat.id,
                    title=f'Upgrade to {requested_plan["name"]}',
                    description=f'Upgrade your subscription to {requested_plan["name"]} plan with the following features: {", ".join(requested_plan["features"])}.',
                    payload=f'{requested_plan_index}',
                    provider_token='YOUR_PROVIDER_TOKEN_HERE',  # Replace with your actual provider token
                    currency='USD',
                    prices=[LabeledPrice(label=f'{requested_plan["name"]} Subscription Price', amount=requested_plan['price'] * 100)],  # Amount is in cents
                    start_parameter=f'upgrade-{requested_plan["name"]}'
                )
        return INVOICE_SENT
    
    async def prechechout_callback(update: Update, context:ContextTypes.DEFAULT_TYPE):
        update.pre_checkout_query.answer(ok=True)
        return WAITING_FOR_PAYMENT
    
    async def subscribe(update: Update, context:ContextTypes.DEFAULT_TYPE):
        user_plans.subscribe(userid=update.effective_user.id,plan=update.message.successful_payment.invoice_payload)
        return ConversationHandler.END

video_inputs_handler = ConversationHandler(
    entry_points=[
        CommandHandler('transcode', transcoder.start),
        CallbackQueryHandler(transcoder.start, pattern='^transcode$'),
        MessageHandler(filters.VIDEO, transcoder.video_file)],
    states={
        UPLOADING_VIDEO: [MessageHandler(filters.VIDEO & ~filters.COMMAND, transcoder.video_file)],
        UPLOADING_LOGO: [
            MessageHandler(filters.PHOTO & ~filters.COMMAND, transcoder.logo_file),
            CallbackQueryHandler(transcoder.skip_logo, pattern='^skip_logo$')
            ],
        UPLOADING_SUBTITLE: [
            MessageHandler(filters.Document.ALL & ~filters.COMMAND, transcoder.subtitle_file),
            CallbackQueryHandler(transcoder.skip_subtitle, pattern='^skip_subtitle$')
            ],
    },
    fallbacks=[
        CommandHandler('cancel', bot_map.end_conversation),
        CallbackQueryHandler(bot_map.end_conversation, pattern='^cancel$'),
        MessageHandler(filters.ALL & ~filters.COMMAND, bot_map.end_conversation),
        CallbackQueryHandler(bot_map.end_conversation)
    ],       
    conversation_timeout= 15 * 60 
)

plan_menu_handler = ConversationHandler(
    entry_points=[
        CommandHandler('plan', Plan.start),
        CallbackQueryHandler(Plan.start, pattern='^plan$')
        ],
    states={
        USER_PLAN_SUBSCRIPTION: [
            CallbackQueryHandler(callback=Plan.show_plan, pattern='^plan.*$'),
            CallbackQueryHandler(callback=bot_map.end_conversation, pattern='^back$')
            ],
        SHOWING_PLAN : [
            CallbackQueryHandler(callback=Plan.send_Invoice,pattern='^upgradeplan.*$'),
            CallbackQueryHandler(callback=Plan.back_to_mainmenu, pattern='^back$')
            ],
        INVOICE_SENT : [PreCheckoutQueryHandler(callback=Plan.prechechout_callback)],
        WAITING_FOR_PAYMENT: [MessageHandler(filters=filters.SUCCESSFUL_PAYMENT, callback=Plan.subscribe)]
    },
    fallbacks=[
        CommandHandler('cancel', bot_map.end_conversation),
        CallbackQueryHandler(bot_map.end_conversation, pattern='^cancel$'),
        MessageHandler(filters.ALL & ~filters.COMMAND, bot_map.end_conversation),
        CallbackQueryHandler(bot_map.end_conversation)
    ],
    conversation_timeout= 15 * 60 
)