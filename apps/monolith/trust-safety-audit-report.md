# Trust & Safety Audit Report - BisouLand

**Date:** 2025-08-13  
**Auditor:** Claude Code  
**Application:** BisouLand LAMP Application (2005)  
**Scope:** Identification of harassment vectors and Trust & Safety issues

## Executive Summary

This audit identified multiple features in the BisouLand application that present significant Trust & Safety risks, enabling various forms of user harassment including direct messaging, stalking, and forced interactions. The application lacks basic user protection mechanisms such as blocking, reporting, or moderation tools.

## Critical Findings

### **CRITICAL - Direct Communication Features**

#### 1. Private Messaging System
- **Files:** `messages` table (schema.sql:47-55), `boite.php`, `lire.php`
- **Risk:** Users can send unlimited private messages to any other user
- **Details:**
  - Messages stored with `posteur` (sender) and `destin` (recipient) fields
  - No blocking/filtering mechanism visible
  - AdminMP function automatically sends system messages to users
  - Users can view, delete messages but cannot block senders

### **HIGH RISK - Player Tracking & Stalking Features**

#### 2. Player Search System
- **Files:** `recherche.php`
- **Risk:** Enables targeted harassment through user discovery
- **Details:**
  - Anyone can search for any username
  - Returns online status and location (nuage number)
  - Facilitates targeted harassment of specific users

#### 3. Player Discovery System
- **Files:** `membres.php`
- **Risk:** Mass user enumeration and targeting
- **Details:**
  - Lists all registered players with online status
  - Shows location information for logged-in users
  - Paginated list enables systematic user discovery

#### 4. "Dévisager" (Stalking) System
- **Files:** `yeux.php`
- **Risk:** Asymmetric surveillance enabling stalking behavior
- **Details:**
  - Players can spy on other players using "eyes" game mechanic
  - Reveals detailed player statistics and resources
  - Automatically notifies target: "X t'a dévisagé" (X stared at you)
  - Creates power imbalance through information asymmetry

### **MEDIUM RISK - Forced Interaction**

#### 5. Attack System
- **Files:** `attaque.php`, `attaque` table (schema.sql:128-138)
- **Risk:** Forces unwanted PvP interactions
- **Details:**
  - Forces unwanted interactions between players
  - Automatically sends threatening messages: "X vient d'envoyer ses bisous dans ta direction"
  - Blocks attacker's account during attack, but forces interaction on victim
  - 12-hour cooldown insufficient to prevent sustained harassment campaigns
  - Attack results sent via private messages

### **LOW RISK - Public Spaces**

#### 6. Guest Book Systems
- **Files:** `livreor.php`, `orbisous` table (schema.sql:88-96, 111-117)
- **Risk:** Public harassment potential
- **Details:**
  - Currently disabled but functional code exists
  - Public message boards without visible moderation
  - Could enable public harassment/shaming if reactivated

#### 7. News System
- **Files:** `newsbisous` table (schema.sql:99-106)
- **Risk:** Potential for targeted messaging abuse
- **Details:**
  - Admin-controlled but could be misused for public targeting
  - No user content filtering visible

## Additional Security & Safety Concerns

- **No User Protection Tools:** No visible reporting, blocking, or muting systems
- **No Moderation Framework:** Limited admin tools, no systematic moderation
- **Privacy Issues:** User location data publicly accessible to logged-in users
- **Real-time Tracking:** Online status tracking enables stalking pattern analysis
- **Information Disclosure:** Detailed player statistics available through "dévisager"

## Database Schema Analysis

The following tables enable harassment vectors:

- `messages`: Direct private messaging without restrictions
- `membres`: Contains user location and status data
- `attaque`: Enables forced interactions
- `logatt`: Attack history for rate limiting (insufficient protection)
- `livreor`/`orbisous`: Public messaging (currently disabled)

## Recommended Actions

### Immediate Priority (Critical/High Risk)
1. **Disable Private Messaging System** - Remove or heavily restrict message sending
2. **Remove Player Search** - Eliminate username search functionality  
3. **Disable Stalking System** - Remove "dévisager" feature entirely
4. **Restrict Player Lists** - Limit visibility of other users
5. **Review Attack System** - Consider disabling or adding opt-out mechanism

### Medium Priority
1. **Implement Blocking System** - Allow users to block others
2. **Add Reporting Mechanism** - Enable harassment reporting
3. **Enhance Privacy Controls** - Hide user status/location data
4. **Add Rate Limiting** - Stricter cooldowns on interactions

### Long-term
1. **Content Moderation System** - Implement automated and manual moderation
2. **User Safety Education** - Add safety guidelines and resources
3. **Audit Logging** - Track potential harassment patterns

## Conclusion

The BisouLand application contains multiple features that create significant harassment risks, particularly the private messaging, user discovery, and stalking systems. The lack of basic user protection mechanisms compound these risks. Immediate action should be taken to disable or heavily modify the highest-risk features identified in this audit.