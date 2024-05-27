from include import *

plans_buttons = []

for plan_instance in plans:
    plans_buttons.append([InlineKeyboardButton(text=f'{plan_instance.name}', callback_data=f'show{plan_instance.name}')])

class Transcoder():
    async def start(update: Update, context:ContextTypes.DEFAULT_TYPE):
        await context.bot.send_message(chat_id=update.effective_chat.id,text='ello')
        return UPLOADING_VIDEO

    async def video_file(update: Update, context:ContextTypes.DEFAULT_TYPE):
        return UPLOADING_LOGO

    async def skip_logo(update: Update, context:ContextTypes.DEFAULT_TYPE):
        return UPLOADING_SUBTITLE

    async def logo_file(update: Update, context:ContextTypes.DEFAULT_TYPE):
        return UPLOADING_SUBTITLE

    async def skip_subtitle(update: Update, context:ContextTypes.DEFAULT_TYPE):
        pass

    async def subtitle_file(update: Update, context:ContextTypes.DEFAULT_TYPE):
        pass

    async def cancel(update: Update, context:ContextTypes.DEFAULT_TYPE):
        pass

class Plan():
    async def start(update: Update, context:ContextTypes.DEFAULT_TYPE):
        user: Users = user_plans.get_user(userid=str(update.effective_user.id))
        await context.bot.send_message(chat_id=update.effective_chat.id,text=f'User ID: {update.effective_user.id}\nName: {update.effective_user.full_name}\n\n💠Active Plan: {user.plan.name}\n\nFeatures:\n✓ {'\n✓ '.join(feature for feature in user.plan.features)}', reply_markup=InlineKeyboardMarkup(plans_buttons))
        return CURRENT_PLAN
    
    async def cancel():
        pass

    
video_inputs_handler = ConversationHandler(
    entry_points=[
        CommandHandler('transcode', Transcoder.start),
        CallbackQueryHandler(Transcoder.start, pattern='^transcode$')],
    states={
        UPLOADING_VIDEO: [MessageHandler(filters.VIDEO & ~filters.COMMAND, Transcoder.video_file)],
        UPLOADING_LOGO: [
            MessageHandler(filters.PHOTO & ~filters.COMMAND, Transcoder.logo_file),
            CallbackQueryHandler(Transcoder.skip_logo, pattern='^skip_logo$')
            ],
        UPLOADING_SUBTITLE: [
            MessageHandler(filters.Document.ALL & ~filters.COMMAND, Transcoder.subtitle_file),
            CallbackQueryHandler(Transcoder.skip_subtitle, pattern='^skip_subtitle$')
            ],
    },
    fallbacks=[CommandHandler('cancel', Transcoder.cancel)],
)

plan_menu_handler = ConversationHandler(
    entry_points=[
        CommandHandler('plan', Plan.start),
        CallbackQueryHandler(Plan.start, pattern='^plan$')],
    states={
        CURRENT_PLAN: []
    },
    fallbacks=[CommandHandler('cancel', Plan.cancel)],
)