import pickle
import json
import datetime
import schedule

api_id = '2832994'
api_hash = 'c52da6a771b2134a2fd58d79ac5c7e9d'
bot_token = '7189345969:AAH6NnFud_Ey6DfDUsi0w_MReJ-jE0wibuU'

plans = [
    {
        'name':'Basic',
        'price': 5,
        'features': [
            'Feature 1',
            'Feature 2',
            'Feature 3',
            'Feature 4',
            'Feature 5'
        ]
    },
    {
        'name':'Standard',
        'price': 15,
        'features': [
            'Feature 1',
            'Feature 2',
            'Feature 3',
            'Feature 4',
            'Feature 5'
        ]
    },
    {
        'name':'Premium',
        'price': 35,
        'features': [
            'Feature 1',
            'Feature 2',
            'Feature 3',
            'Feature 4',
            'Feature 5'
        ]
    },
]

class Users:
    def __init__(self):     
        try:
            with open('database.json', 'r') as file:
                self.users = json.load(file)
        except (FileNotFoundError):
            self.users = {}

    def save_json(self):
        with open('database.json', 'w') as file:
            json.dump(self.users, file)

    def add_user(self, userid: str, username: str):
        if userid not in self.users:
            self.users[userid] = {
                'userid': userid,
                'username': username,
                'plan': plans[0],
                'startdate': datetime.datetime.now().date().isoformat()
                }
        self.save_json()

    def subscribe(self, userid: str, plan):
        if userid in self.users:
            self.users[userid]['plan'] = plan
            self.users[userid]['startdate'] = datetime.datetime.now().date().isoformat()
        self.save_json()

    def get_user(self, userid: str):
        if userid in self.users:
            return self.users[userid]
        return False

    def unsubscribe_expired_users(self):
        for userid, user in list(self.users.items()):  # Use list() to avoid modifying dict during iteration
            if user.start_date:
                if datetime.datetime.now() - user.start_date > datetime.timedelta(days=30):
                    self.users[userid].plan = plans[0]
        self.save_json()

user_plans = Users()

schedule.every().day.at("00:01").do(job_func=user_plans.unsubscribe_expired_users)