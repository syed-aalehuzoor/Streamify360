from telegram.ext import (
    ApplicationBuilder,
    CommandHandler,
    ContextTypes,
    filters,
    MessageHandler,
    CallbackQueryHandler,
    CallbackContext,
    ConversationHandler,
    )
from telegram import (
    Update,
    InlineKeyboardButton,
    InlineKeyboardMarkup,
    ReplyKeyboardMarkup,
    KeyboardButton,
    Bot,
    )

UPLOADING_VIDEO, UPLOADING_LOGO, UPLOADING_SUBTITLE = range(6)

class Transcoder():
    async def transcode(update: Update, context:ContextTypes.DEFAULT_TYPE):
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