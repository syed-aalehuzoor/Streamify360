from telegram.ext import ApplicationBuilder, CommandHandler, ContextTypes, filters, MessageHandler, CallbackQueryHandler, CallbackContext, ConversationHandler
from telegram import Update, InlineKeyboardButton, InlineKeyboardMarkup,ReplyKeyboardMarkup, KeyboardButton, Bot
from include import Transcoder, CHOOSING, TYPING_NAME, TYPING_RESOLUTION, UPLOADING_VIDEO, UPLOADING_LOGO, UPLOADING_SUBTITLE
CHOOSING, TYPING_NAME, TYPING_RESOLUTION, UPLOADING_VIDEO, UPLOADING_LOGO, UPLOADING_SUBTITLE = range(6)

state = None
app = ApplicationBuilder().token("7189345969:AAH6NnFud_Ey6DfDUsi0w_MReJ-jE0wibuU").build()

async def start(update: Update, context:ContextTypes.DEFAULT_TYPE):
    pass

video_inputs_handler = ConversationHandler(
    entry_points=[
        CommandHandler('transcode', Transcoder.transcode),
        CallbackQueryHandler(Transcoder.transcode, pattern='^transcode$')],
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
app.add_handler(video_inputs_handler)
app.add_handler(CommandHandler("start",start))
app.run_polling()