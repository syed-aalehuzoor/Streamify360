from include import *

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
        await context.bot.send_message(chat_id=update.effective_chat.id,text='ello')
        return CURRENT_PLAN
    
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
    fallbacks=[CommandHandler('cancel', Transcoder.cancel)],
)